<?php

namespace App\Http\Controllers\Api\Admin;

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
        $query = HomeAd::query()->ordered();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $ads = $query->paginate($request->get('per_page', 15));

        return $this->successResponse($ads);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:banner,offer',
            'image' => 'required|image|max:2048', // Max 2MB
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $data = $validator->validated();

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('home_ads', 'public');
            $data['image'] = $path;
        }

        $ad = HomeAd::create($data);

        return $this->successResponse($ad, 'success.created', [], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $ad = HomeAd::find($id);

        if (!$ad) {
            return $this->notFoundResponse();
        }

        return $this->successResponse($ad);
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

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:banner,offer',
            'image' => 'nullable|image|max:2048',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $data = $validator->validated();

        if ($request->hasFile('image')) {
            // Delete old image
            if ($ad->image && Storage::disk('public')->exists($ad->image)) {
                Storage::disk('public')->delete($ad->image);
            }
            
            $path = $request->file('image')->store('home_ads', 'public');
            $data['image'] = $path;
        }

        $ad->update($data);

        return $this->successResponse($ad, 'success.updated');
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

        if ($ad->image && Storage::disk('public')->exists($ad->image)) {
            Storage::disk('public')->delete($ad->image);
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

        return $this->successResponse($ad, 'success.updated');
    }
}
