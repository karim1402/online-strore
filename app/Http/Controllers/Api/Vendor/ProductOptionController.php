<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\OptionGroup;
use App\Models\OptionValue;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductOptionController extends Controller
{
    use ApiResponse;

    /**
     * Get all options for a product (vendor's store only)
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

            $productOptions = ProductOption::where('product_id', $productId)
                ->with(['optionGroup.values', 'productOptionValues.optionValue'])
                ->orderBy('sort_order', 'asc')
                ->get();

            return $this->successResponse($productOptions, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Assign an option group to a product (vendor's store only)
     */
    public function assignOptionGroup(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'product_id' => 'required|integer|exists:products,id',
                'option_group_id' => 'required|integer|exists:option_groups,id',
                'is_required' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Verify product belongs to vendor's store
            $product = Product::where('store_id', $storeId)
                ->where('id', $request->product_id)
                ->first();

            if (!$product) {
                return $this->errorResponse('errors.product_not_found', [], 404);
            }

            // Check if already assigned
            $existing = ProductOption::where('product_id', $request->product_id)
                ->where('option_group_id', $request->option_group_id)
                ->first();

            if ($existing) {
                return $this->errorResponse('errors.option_group_already_assigned', [], 409);
            }

            $productOption = ProductOption::create([
                'product_id' => $request->product_id,
                'option_group_id' => $request->option_group_id,
                'is_required' => $request->boolean('is_required', false),
                'sort_order' => $request->get('sort_order', 0),
            ]);

            $productOption->load(['optionGroup.values', 'productOptionValues']);

            DB::commit();

            return $this->successResponse($productOption, 'success.option_group_assigned', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update product option group settings (vendor's store only)
     */
    public function updateOptionGroup(Request $request, $id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $productOption = ProductOption::whereHas('product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($id);

            if (!$productOption) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'is_required' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            if ($request->has('is_required')) {
                $productOption->is_required = $request->boolean('is_required');
            }
            if ($request->has('sort_order')) {
                $productOption->sort_order = $request->sort_order;
            }

            $productOption->save();
            $productOption->load(['optionGroup.values', 'productOptionValues']);

            DB::commit();

            return $this->successResponse($productOption, 'success.option_group_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Remove an option group from a product (vendor's store only)
     */
    public function removeOptionGroup($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $productOption = ProductOption::whereHas('product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($id);

            if (!$productOption) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();
            $productOption->delete();
            DB::commit();

            return $this->successResponse(null, 'success.option_group_removed');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Assign option values with pricing and stock to a product option (vendor's store only)
     */
    public function assignOptionValues(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'product_option_id' => 'required|integer|exists:product_options,id',
                'option_values' => 'required|array',
                'option_values.*.option_value_id' => 'required|integer|exists:option_values,id',
                'option_values.*.price_type' => 'required|in:fixed,additional,percentage',
                'option_values.*.price_value' => 'required|numeric|min:0',
                'option_values.*.stock_quantity' => 'nullable|integer|min:0',
                'option_values.*.is_available' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $productOption = ProductOption::whereHas('product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($request->product_option_id);

            if (!$productOption) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $createdValues = [];

            foreach ($request->option_values as $valueData) {
                // Check if option value belongs to the same option group
                $optionValue = OptionValue::where('id', $valueData['option_value_id'])
                    ->where('option_group_id', $productOption->option_group_id)
                    ->first();

                if (!$optionValue) {
                    DB::rollBack();
                    return $this->errorResponse('errors.option_value_not_in_group', [], 400);
                }

                // Check if already assigned
                $existing = ProductOptionValue::where('product_option_id', $request->product_option_id)
                    ->where('option_value_id', $valueData['option_value_id'])
                    ->first();

                if ($existing) {
                    continue;
                }

                $productOptionValue = ProductOptionValue::create([
                    'product_option_id' => $request->product_option_id,
                    'option_value_id' => $valueData['option_value_id'],
                    'price_type' => $valueData['price_type'],
                    'price_value' => $valueData['price_value'],
                    'stock_quantity' => $valueData['stock_quantity'] ?? 0,
                    'is_available' => $valueData['is_available'] ?? true,
                ]);

                $productOptionValue->load('optionValue');
                $createdValues[] = $productOptionValue;
            }

            DB::commit();

            return $this->successResponse($createdValues, 'success.option_values_assigned', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update a single product option value (vendor's store only)
     */
    public function updateOptionValue(Request $request, $id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $productOptionValue = ProductOptionValue::whereHas('productOption.product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($id);

            if (!$productOptionValue) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'price_type' => 'nullable|in:fixed,additional,percentage',
                'price_value' => 'nullable|numeric|min:0',
                'stock_quantity' => 'nullable|integer|min:0',
                'is_available' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            if ($request->filled('price_type')) {
                $productOptionValue->price_type = $request->price_type;
            }
            if ($request->has('price_value')) {
                $productOptionValue->price_value = $request->price_value;
            }
            if ($request->has('stock_quantity')) {
                $productOptionValue->stock_quantity = $request->stock_quantity;
            }
            if ($request->has('is_available')) {
                $productOptionValue->is_available = $request->boolean('is_available');
            }

            $productOptionValue->save();
            $productOptionValue->load('optionValue');

            DB::commit();

            return $this->successResponse($productOptionValue, 'success.option_value_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Remove an option value from a product (vendor's store only)
     */
    public function removeOptionValue($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $productOptionValue = ProductOptionValue::whereHas('productOption.product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($id);

            if (!$productOptionValue) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();
            $productOptionValue->delete();
            DB::commit();

            return $this->successResponse(null, 'success.option_value_removed');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update stock quantity for an option value (vendor's store only)
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $productOptionValue = ProductOptionValue::whereHas('productOption.product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($id);

            if (!$productOptionValue) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'stock_quantity' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $productOptionValue->stock_quantity = $request->stock_quantity;
            $productOptionValue->save();
            $productOptionValue->load('optionValue');

            return $this->successResponse($productOptionValue, 'success.stock_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
