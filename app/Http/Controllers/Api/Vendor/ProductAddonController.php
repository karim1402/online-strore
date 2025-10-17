<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\Addon;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAddonController extends Controller
{
    use ApiResponse;

    /**
     * Get all addons for a product (vendor's store only)
     */
    public function index($productId): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::where('store_id', $storeId)->find($productId);

            if (!$product) {
                return $this->errorResponse('errors.product_not_found', [], 404);
            }

            $productAddons = $product->addons()
                ->orderBy('product_addons.sort_order', 'asc')
                ->get();

            return $this->successResponse($productAddons, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Assign addons to a product (vendor's store only)
     */
    public function assignAddons(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
                'addon_ids' => 'required|array',
                'addon_ids.*' => 'required|integer|exists:addons,id',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $product = Product::where('store_id', $storeId)
                ->where('id', $request->product_id)
                ->first();

            if (!$product) {
                return $this->errorResponse('errors.product_not_found', [], 404);
            }

            // Verify addons belong to vendor's store
            $addons = Addon::whereIn('id', $request->addon_ids)
                ->where('store_id', $storeId)
                ->get();

            if ($addons->count() !== count($request->addon_ids)) {
                return $this->errorResponse('errors.some_addons_not_in_store', [], 400);
            }

            // Attach addons
            foreach ($request->addon_ids as $index => $addonId) {
                if (!$product->addons()->where('addon_id', $addonId)->exists()) {
                    $product->addons()->attach($addonId, [
                        'is_available' => true,
                        'sort_order' => $index,
                    ]);
                }
            }

            $product->load('addons');

            DB::commit();

            return $this->successResponse($product->addons, 'success.addons_assigned', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update addon settings for a product (vendor's store only)
     */
    public function updateAddon(Request $request, $productId, $addonId): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'is_available' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $product = Product::where('store_id', $storeId)->find($productId);

            if (!$product) {
                return $this->errorResponse('errors.product_not_found', [], 404);
            }

            if (!$product->addons()->where('addon_id', $addonId)->exists()) {
                return $this->errorResponse('errors.addon_not_assigned', [], 404);
            }

            $updateData = [];
            if ($request->has('is_available')) {
                $updateData['is_available'] = $request->boolean('is_available');
            }
            if ($request->has('sort_order')) {
                $updateData['sort_order'] = $request->sort_order;
            }

            $product->addons()->updateExistingPivot($addonId, $updateData);

            DB::commit();

            return $this->successResponse(null, 'success.addon_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Remove an addon from a product (vendor's store only)
     */
    public function removeAddon($productId, $addonId): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::where('store_id', $storeId)->find($productId);

            if (!$product) {
                return $this->errorResponse('errors.product_not_found', [], 404);
            }

            DB::beginTransaction();
            $product->addons()->detach($addonId);
            DB::commit();

            return $this->successResponse(null, 'success.addon_removed');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Reorder product addons (vendor's store only)
     */
    public function reorderAddons(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
                'addons' => 'required|array',
                'addons.*.addon_id' => 'required|integer|exists:addons,id',
                'addons.*.sort_order' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $product = Product::where('store_id', $storeId)
                ->where('id', $request->product_id)
                ->first();

            if (!$product) {
                return $this->errorResponse('errors.product_not_found', [], 404);
            }

            foreach ($request->addons as $addonData) {
                $product->addons()->updateExistingPivot($addonData['addon_id'], [
                    'sort_order' => $addonData['sort_order']
                ]);
            }

            DB::commit();

            return $this->successResponse(null, 'success.addons_reordered');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
