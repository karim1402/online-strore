<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Store;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddonController extends Controller
{
    use ApiResponse;

    /**
     * Get all addons with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Addon::with('store:id,name_en,name_ar');

            // Filter by store
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            // Filter by addon category
            if ($request->filled('addon_category')) {
                $query->where('addon_category', $request->addon_category);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Search by name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%");
                });
            }

            $addons = $query->orderBy('created_at', 'desc')->paginate(15);

            return $this->successResponse($addons, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single addon by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $addon = Addon::with('store:id,name_en,name_ar')->find($id);

            if (!$addon) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($addon, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new addon
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'store_id' => 'required|integer|exists:stores,id',
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'addon_category' => 'nullable|string|max:50',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Verify store exists
            $store = Store::find($request->store_id);
            if (!$store) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $addon = Addon::create([
                'store_id' => $request->store_id,
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'description_en' => $request->description_en,
                'description_ar' => $request->description_ar,
                'price' => $request->price,
                'addon_category' => $request->addon_category,
                'is_active' => $request->boolean('is_active', true),
            ]);

            $addon->load('store');

            DB::commit();

            return $this->successResponse($addon, 'success.addon_created', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update an addon
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $addon = Addon::find($id);

            if (!$addon) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'addon_category' => 'nullable|string|max:50',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            if ($request->filled('name_en')) {
                $addon->name_en = $request->name_en;
            }
            if ($request->filled('name_ar')) {
                $addon->name_ar = $request->name_ar;
            }
            if ($request->has('description_en')) {
                $addon->description_en = $request->description_en;
            }
            if ($request->has('description_ar')) {
                $addon->description_ar = $request->description_ar;
            }
            if ($request->filled('price')) {
                $addon->price = $request->price;
            }
            if ($request->has('addon_category')) {
                $addon->addon_category = $request->addon_category;
            }
            if ($request->has('is_active')) {
                $addon->is_active = $request->boolean('is_active');
            }

            $addon->save();
            $addon->load('store');

            DB::commit();

            return $this->successResponse($addon, 'success.addon_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete an addon
     */
    public function destroy($id): JsonResponse
    {
        try {
            $addon = Addon::find($id);

            if (!$addon) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            $addon->delete();

            DB::commit();

            return $this->successResponse(null, 'success.addon_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle addon active status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $addon = Addon::find($id);

            if (!$addon) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $addon->is_active = !$addon->is_active;
            $addon->save();
            $addon->load('store');

            return $this->successResponse($addon, 'success.status_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get addon categories for a store
     */
    public function getCategories($storeId): JsonResponse
    {
        try {
            $categories = Addon::where('store_id', $storeId)
                ->whereNotNull('addon_category')
                ->select('addon_category')
                ->distinct()
                ->pluck('addon_category');

            return $this->successResponse($categories, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
