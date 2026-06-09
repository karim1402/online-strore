<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeAd;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HomeAdController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = HomeAd::query()->with(['module', 'products'])->ordered();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $ads = $query->paginate($request->get('per_page', 15));

        $ads->through(fn($ad) => $this->transformAd($ad));

        return $this->successResponse($ads);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:banner,offer',
            'link_type' => 'required_if:type,offer|nullable|in:module,product',
            'module_id' => 'required_if:link_type,module|nullable|exists:modules,id',
            'product_ids' => 'required_if:link_type,product|nullable|array',
            'product_ids.*' => 'exists:products,id',
            'image' => 'required|image', // Max 2MB
            'image_ar' => 'nullable|image',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $data = $validator->validated();

        // Banner type has no link
        if ($request->type === 'banner') {
            $data['link_type'] = null;
            $data['module_id'] = null;
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('home_ads/v2', 'r2');
            $data['image_v2'] = $path;
            unset($data['image']);
        }

        // Handle Arabic Image Upload
        if ($request->hasFile('image_ar')) {
            $path = $request->file('image_ar')->store('home_ads/v2', 'r2');
            $data['image_ar_v2'] = $path;
            unset($data['image_ar']);
        }

        $ad = HomeAd::create($data);

        if ($ad->link_type === 'product' && $request->has('product_ids')) {
            $ad->products()->sync($request->product_ids);
        }

        return $this->successResponse($this->transformAd($ad->load(['module', 'products'])), 'success.created', [], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $ad = HomeAd::with(['module', 'products'])->find($id);

        if (!$ad) {
            return $this->notFoundResponse();
        }

        return $this->successResponse($this->transformAd($ad));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $ad = HomeAd::find($id);

        if (!$ad) {
            return $this->notFoundResponse();
        }

        $effectiveType = $request->input('type', $ad->type);

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:banner,offer',
            'link_type' => ($effectiveType === 'offer' ? 'required' : 'nullable') . '|nullable|in:module,product',
            'module_id' => 'required_if:link_type,module|nullable|exists:modules,id',
            'product_ids' => 'required_if:link_type,product|nullable|array',
            'product_ids.*' => 'exists:products,id',
            'image' => 'nullable|image',
            'image_ar' => 'nullable|image',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $data = $validator->validated();

        // Banner type has no link — force nulls
        if ($effectiveType === 'banner') {
            $data['link_type'] = null;
            $data['module_id'] = null;
        }

        if ($request->hasFile('image')) {
            // Delete old V2 image
            if ($ad->image_v2 && Storage::disk('r2')->exists($ad->image_v2)) {
                Storage::disk('r2')->delete($ad->image_v2);
            }

            $path = $request->file('image')->store('home_ads/v2', 'r2');
            $data['image_v2'] = $path;
            unset($data['image']);
        }

        if ($request->hasFile('image_ar')) {
            if ($ad->image_ar_v2) {
                Storage::disk('public')->exists($ad->image_ar_v2) && Storage::disk('public')->delete($ad->image_ar_v2);
                Storage::disk('r2')->exists($ad->image_ar_v2) && Storage::disk('r2')->delete($ad->image_ar_v2);
            }
            $path = $request->file('image_ar')->store('home_ads/v2', 'r2');
            $data['image_ar_v2'] = $path;
            unset($data['image_ar']);
        }

        $ad->update($data);

        // Sync products only for offer type
        if ($effectiveType === 'banner') {
            // Banner has no linked products
            $ad->products()->detach();
        } elseif ($ad->fresh()->link_type === 'product') {
            if ($request->has('product_ids')) {
                $ad->products()->sync($request->product_ids);
            }
        } else {
            // link_type changed to module or something else — remove products
            $ad->products()->detach();
        }

        return $this->successResponse($this->transformAd($ad->load(['module', 'products'])), 'success.updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $ad = HomeAd::find($id);

        if (!$ad) {
            return $this->notFoundResponse();
        }

        foreach (['image', 'image_ar', 'image_v2', 'image_ar_v2'] as $field) {
            if ($ad->$field) {
                Storage::disk('public')->exists($ad->$field) && Storage::disk('public')->delete($ad->$field);
                Storage::disk('r2')->exists($ad->$field) && Storage::disk('r2')->delete($ad->$field);
            }
        }

        $ad->delete();

        return $this->successResponse(null, 'success.deleted');
    }

    /**
     * Toggle the active status of the ad.
     */
    public function toggleStatus($id): JsonResponse
    {
        $ad = HomeAd::find($id);

        if (!$ad) {
            return $this->notFoundResponse();
        }

        $ad->update(['is_active' => !$ad->is_active]);

        return $this->successResponse($this->transformAd($ad->load(['module', 'products'])), 'success.updated');
    }

    private function transformAd(HomeAd $ad): array
    {
        $data = $ad->toArray();
        $data['image_url'] = $ad->image_v2_url ?? $ad->image_url;
        $data['image_ar_url'] = $ad->image_ar_v2_url ?? $ad->image_ar_url;
        return $data;
    }
}
