<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\Addon;
use App\Models\Module;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAddonController extends Controller
{
    use ApiResponse;

    /**
     * Get all addons for a product
     */
    public function index($productId): JsonResponse
    {
        try {
            $product = Product::find($productId);

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
     * Assign addons to a product
     */
    public function assignAddons(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
                'addon_ids' => 'required|array',
                'addon_ids.*' => 'required|integer|exists:addons,id',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $product = Product::find($request->product_id);

            // Verify addons belong to same store as product or are global
            $addons = Addon::whereIn('id', $request->addon_ids)
                ->where(function ($query) use ($product) {
                    $query->where('store_id', $product->store_id)
                        ->orWhereNull('store_id');
                })
                ->get();

            if ($addons->count() !== count($request->addon_ids)) {
                return $this->errorResponse('errors.some_addons_invalid', [], 400);
            }

            // Attach addons with default values
            foreach ($request->addon_ids as $index => $addonId) {
                // Skip if already assigned
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
     * Update addon settings for a product
     */
    public function updateAddon(Request $request, $productId, $addonId): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'is_available' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $product = Product::find($productId);

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
     * Remove an addon from a product
     */
    public function removeAddon($productId, $addonId): JsonResponse
    {
        try {
            $product = Product::find($productId);

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
     * Reorder product addons
     */
    public function reorderAddons(Request $request): JsonResponse
    {
        try {
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

            $product = Product::find($request->product_id);

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

    /**
     * Assign addons to all products in a module
     */
    public function assignAddonsToModule(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'module_id' => 'required|integer|exists:modules,id',
                'addon_ids' => 'required|array',
                'addon_ids.*' => 'required|integer|exists:addons,id',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $module = Module::find($request->module_id);

            if (!$module) {
                return $this->errorResponse('errors.module_not_found', [], 404);
            }

            // Get all product IDs in this module (through categories)
            $categoryIds = $module->categories()->pluck('id');
            $products = Product::whereIn('category_id', $categoryIds)->get();

            if ($products->isEmpty()) {
                return $this->errorResponse('errors.no_products_in_module', [], 404);
            }

            $affectedCount = 0;

            foreach ($products as $product) {
                foreach ($request->addon_ids as $index => $addonId) {
                    // Skip if already assigned
                    if (!$product->addons()->where('addon_id', $addonId)->exists()) {
                        $product->addons()->attach($addonId, [
                            'is_available' => true,
                            'sort_order' => $index,
                        ]);
                    }
                }
                $affectedCount++;
            }

            DB::commit();

            return $this->successResponse([
                'affected_products' => $affectedCount,
                'addon_ids' => $request->addon_ids,
            ], 'success.addons_assigned', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Remove addons from all products in a module
     */
    public function removeAddonsFromModule(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'module_id' => 'required|integer|exists:modules,id',
                'addon_ids' => 'required|array',
                'addon_ids.*' => 'required|integer|exists:addons,id',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $module = Module::find($request->module_id);

            if (!$module) {
                return $this->errorResponse('errors.module_not_found', [], 404);
            }

            // Get all product IDs in this module (through categories)
            $categoryIds = $module->categories()->pluck('id');
            $products = Product::whereIn('category_id', $categoryIds)->get();

            if ($products->isEmpty()) {
                return $this->errorResponse('errors.no_products_in_module', [], 404);
            }

            $affectedCount = 0;

            foreach ($products as $product) {
                $detached = $product->addons()->detach($request->addon_ids);
                if ($detached > 0) {
                    $affectedCount++;
                }
            }

            DB::commit();

            return $this->successResponse([
                'affected_products' => $affectedCount,
                'addon_ids' => $request->addon_ids,
            ], 'success.operation_successful', [], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
