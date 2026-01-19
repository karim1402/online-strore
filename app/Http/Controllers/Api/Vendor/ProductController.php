<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\Category;
use App\Models\OptionValue;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * Get all products for vendor's store
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;
          

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $query = Product::with(['store:id,name_en,name_ar', 'category:id,name_en,name_ar', 'subcategory:id,name_en,name_ar', 'images'])
                ->where('store_id', $storeId);

            // Filter by category
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
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
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('search_keywords', 'like', "%{$search}%");
                });
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            if (in_array($sortBy, ['created_at', 'base_price', 'view_count', 'sales_count', 'sort_order'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            $products = $query->paginate($request->get('per_page', 15));

       

            return $this->successResponse($products, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single product by ID (vendor's store only)
     */
    public function show($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::with([
                'store:id,name_en,name_ar',
                'category:id,name_en,name_ar',
                'subcategory:id,name_en,name_ar',
                'images',
                'productOptions.optionGroup',
                'productOptions.productOptionValues.optionValue',
                'addons'
            ])
            ->where('store_id', $storeId)
            ->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($product, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new product
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'category_id' => 'required|integer|exists:categories,id',
                'subcategory_id' => 'nullable|integer|exists:categories,id',
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'search_keywords' => 'nullable|string',
                'base_price' => 'required|numeric|min:0',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
                'primary_image_index' => 'nullable|integer|min:0',
                'option_groups' => 'nullable|array',
                'option_groups.*.option_group_id' => 'required_with:option_groups|integer|exists:option_groups,id',
                'option_groups.*.is_required' => 'nullable|boolean',
                'option_groups.*.sort_order' => 'nullable|integer|min:0',
                'option_groups.*.option_values' => 'nullable|array',
                'option_groups.*.option_values.*.option_value_id' => 'required_with:option_groups.*.option_values|integer|exists:option_values,id',
                'option_groups.*.option_values.*.price_type' => 'nullable|in:fixed,additional,percentage',
                'option_groups.*.option_values.*.price_value' => 'required_with:option_groups.*.option_values|numeric|min:0',
                'option_groups.*.option_values.*.is_available' => 'nullable|boolean',
                'addon_ids' => 'nullable|array',
                'addon_ids.*' => 'integer|exists:addons,id',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Verify category belongs to one of the modules associated with vendor's store
            $moduleIds = $vendor->store->modules()->pluck('modules.id')->toArray();
            $category = Category::where('id', $request->category_id)
                ->whereIn('module_id', $moduleIds)
                ->first();
            
            if (!$category) {
                return $this->errorResponse('errors.category_not_found_in_store_modules', [], 404);
            }

            // Verify subcategory if provided
            if ($request->filled('subcategory_id')) {
                $subcategory = Category::where('id', $request->subcategory_id)
                    ->where('parent_id', $request->category_id)
                    ->whereIn('module_id', $moduleIds)
                    ->first();
                if (!$subcategory) {
                    return $this->errorResponse('errors.subcategory_not_found_in_category', [], 404);
                }
            }

            // Create product with vendor's store_id
            $product = Product::create([
                'store_id' => $storeId,
                'category_id' => $request->category_id,
                'subcategory_id' => $request->subcategory_id,
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'description_en' => $request->description_en,
                'description_ar' => $request->description_ar,
                'search_keywords' => $request->search_keywords,
                'base_price' => $request->base_price,
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->get('sort_order', 0),
                'metadata' => $request->metadata,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $primaryImageIndex = $request->has('primary_image_index') ? (int)$request->primary_image_index : 0;
                
                // Validate primary_image_index is within range
                if ($primaryImageIndex < 0 || $primaryImageIndex >= count($images)) {
                    $primaryImageIndex = 0;
                }
                
                foreach ($images as $index => $image) {
                    $imagePath = $image->store('products', 'public');
                    
                    // Check if this image index matches the primary_image_index
                    $isPrimary = ($index == $primaryImageIndex);
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_primary' => $isPrimary,
                        'sort_order' => $index,
                    ]);
                }
            }

            // Handle option groups assignment
            if ($request->filled('option_groups')) {
                foreach ($request->option_groups as $optionGroup) {
                    $productOption = ProductOption::where('product_id', $product->id)
                        ->where('option_group_id', $optionGroup['option_group_id'])
                        ->first();

                    if (!$productOption) {
                        $productOption = ProductOption::create([
                            'product_id' => $product->id,
                            'option_group_id' => $optionGroup['option_group_id'],
                            'is_required' => $optionGroup['is_required'] ?? false,
                            'sort_order' => $optionGroup['sort_order'] ?? 0,
                        ]);
                    }

                    // Handle option values if provided
                    if (isset($optionGroup['option_values']) && is_array($optionGroup['option_values'])) {
                        foreach ($optionGroup['option_values'] as $optionValue) {
                            $valueExists = OptionValue::where('id', $optionValue['option_value_id'])
                                ->where('option_group_id', $optionGroup['option_group_id'])
                                ->exists();

                            if ($valueExists) {
                                $povExists = ProductOptionValue::where('product_option_id', $productOption->id)
                                    ->where('option_value_id', $optionValue['option_value_id'])
                                    ->exists();

                                if (!$povExists) {
                                    ProductOptionValue::create([
                                        'product_option_id' => $productOption->id,
                                        'option_value_id' => $optionValue['option_value_id'],
                                        'price_type' => 'fixed', // Always fixed
                                        'price_value' => $optionValue['price_value'],
                                        'is_available' => $optionValue['is_available'] ?? true,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // Handle addons assignment (only vendor's store addons)
            if ($request->filled('addon_ids')) {
                $product->addons()->sync($request->addon_ids);
            }

            $product->load(['store', 'category', 'subcategory', 'images', 'productOptions.optionGroup', 'productOptions.productOptionValues.optionValue', 'addons']);

            DB::commit();

            return $this->successResponse($product, 'success.product_created', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update a product (vendor's store only)
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::where('store_id', $storeId)->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'category_id' => 'nullable|integer|exists:categories,id',
                'subcategory_id' => 'nullable|integer|exists:categories,id',
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'search_keywords' => 'nullable|string',
                'base_price' => 'nullable|numeric|min:0',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
                'option_groups' => 'nullable|array',
                'option_groups.*.option_group_id' => 'required_with:option_groups|integer|exists:option_groups,id',
                'option_groups.*.is_required' => 'nullable|boolean',
                'option_groups.*.sort_order' => 'nullable|integer|min:0',
                'option_groups.*.option_values' => 'nullable|array',
                'option_groups.*.option_values.*.option_value_id' => 'required_with:option_groups.*.option_values|integer|exists:option_values,id',
                'option_groups.*.option_values.*.price_type' => 'nullable|in:fixed,additional,percentage',
                'option_groups.*.option_values.*.price_value' => 'required_with:option_groups.*.option_values|numeric|min:0',
                'option_groups.*.option_values.*.is_available' => 'nullable|boolean',
                'addon_ids' => 'nullable|array',
                'addon_ids.*' => 'integer|exists:addons,id',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Verify category and subcategory if changing
            if ($request->filled('category_id') || $request->filled('subcategory_id')) {
                $categoryId = $request->filled('category_id') ? $request->category_id : $product->category_id;
                $subcategoryId = $request->filled('subcategory_id') ? $request->subcategory_id : $product->subcategory_id;
                $moduleIds = $vendor->store->modules()->pluck('modules.id')->toArray();

                $category = Category::where('id', $categoryId)
                    ->whereIn('module_id', $moduleIds)
                    ->first();
                
                if (!$category) {
                    return $this->errorResponse('errors.category_not_found_in_store_modules', [], 404);
                }

                if ($subcategoryId) {
                    $subcategory = Category::where('id', $subcategoryId)
                        ->where('parent_id', $categoryId)
                        ->whereIn('module_id', $moduleIds)
                        ->first();
                    if (!$subcategory) {
                        return $this->errorResponse('errors.subcategory_not_found_in_category', [], 404);
                    }
                }

                $product->category_id = $categoryId;
                $product->subcategory_id = $subcategoryId;
            }

            // Update product fields
            if ($request->filled('name_en')) {
                $product->name_en = $request->name_en;
            }
            if ($request->filled('name_ar')) {
                $product->name_ar = $request->name_ar;
            }
            if ($request->has('description_en')) {
                $product->description_en = $request->description_en;
            }
            if ($request->has('description_ar')) {
                $product->description_ar = $request->description_ar;
            }
            if ($request->has('search_keywords')) {
                $product->search_keywords = $request->search_keywords;
            }
            if ($request->filled('base_price')) {
                $product->base_price = $request->base_price;
            }
            if ($request->has('is_active')) {
                $product->is_active = $request->boolean('is_active');
            }
            if ($request->has('sort_order')) {
                $product->sort_order = $request->sort_order;
            }
            if ($request->has('metadata')) {
                $product->metadata = $request->metadata;
            }

            $product->save();

            // Handle option groups update
            if ($request->has('option_groups')) {
                if (is_array($request->option_groups) && count($request->option_groups) > 0) {
                    $newOptionGroupIds = collect($request->option_groups)->pluck('option_group_id')->toArray();
                    
                    ProductOption::where('product_id', $product->id)
                        ->whereNotIn('option_group_id', $newOptionGroupIds)
                        ->delete();

                    foreach ($request->option_groups as $optionGroup) {
                        $productOption = ProductOption::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'option_group_id' => $optionGroup['option_group_id']
                            ],
                            [
                                'is_required' => $optionGroup['is_required'] ?? false,
                                'sort_order' => $optionGroup['sort_order'] ?? 0,
                            ]
                        );

                        if (isset($optionGroup['option_values']) && is_array($optionGroup['option_values'])) {
                            $newOptionValueIds = collect($optionGroup['option_values'])->pluck('option_value_id')->toArray();
                            
                            ProductOptionValue::where('product_option_id', $productOption->id)
                                ->whereNotIn('option_value_id', $newOptionValueIds)
                                ->delete();

                            foreach ($optionGroup['option_values'] as $optionValue) {
                                $valueExists = OptionValue::where('id', $optionValue['option_value_id'])
                                    ->where('option_group_id', $optionGroup['option_group_id'])
                                    ->exists();

                                if ($valueExists) {
                                    ProductOptionValue::updateOrCreate(
                                        [
                                            'product_option_id' => $productOption->id,
                                            'option_value_id' => $optionValue['option_value_id']
                                        ],
                                        [
                                            'price_type' => 'fixed', // Always fixed
                                            'price_value' => $optionValue['price_value'],
                                            'is_available' => $optionValue['is_available'] ?? true,
                                        ]
                                    );
                                }
                            }
                        }
                    }
                } else {
                    ProductOption::where('product_id', $product->id)->delete();
                }
            }

            // Handle addons update
            if ($request->has('addon_ids')) {
                if (is_array($request->addon_ids)) {
                    $product->addons()->sync($request->addon_ids);
                } else {
                    $product->addons()->detach();
                }
            }

            $product->load(['store', 'category', 'subcategory', 'images', 'productOptions.optionGroup', 'productOptions.productOptionValues.optionValue', 'addons']);

            DB::commit();

            return $this->successResponse($product, 'success.product_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a product (vendor's store only)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::where('store_id', $storeId)->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();
            $product->delete();
            DB::commit();

            return $this->successResponse(null, 'success.product_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle product active status (vendor's store only)
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::where('store_id', $storeId)->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $product->is_active = !$product->is_active;
            $product->save();
            $product->load(['store', 'category', 'subcategory', 'images']);

            return $this->successResponse($product, 'success.status_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Upload product images (vendor's store only)
     */
    public function uploadImages(Request $request, $id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::where('store_id', $storeId)->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $uploadedImages = [];
            $hasPrimaryImage = $product->images()->where('is_primary', true)->exists();

            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products', 'public');
                $productImage = ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => !$hasPrimaryImage && $index === 0,
                    'sort_order' => $product->images()->count() + $index,
                ]);
                $uploadedImages[] = $productImage;
            }

            DB::commit();

            return $this->successResponse($uploadedImages, 'success.images_uploaded');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a product image (vendor's store only)
     */
    public function deleteImage($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $image = ProductImage::whereHas('product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($id);

            if (!$image) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            $wasPrimary = $image->is_primary;
            $productId = $image->product_id;

            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();

            if ($wasPrimary) {
                $firstImage = ProductImage::where('product_id', $productId)->orderBy('sort_order')->first();
                if ($firstImage) {
                    $firstImage->is_primary = true;
                    $firstImage->save();
                }
            }

            DB::commit();

            return $this->successResponse(null, 'success.image_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Set primary image (vendor's store only)
     */
    public function setPrimaryImage($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $image = ProductImage::whereHas('product', function ($q) use ($storeId) {
                $q->where('store_id', $storeId);
            })->find($id);

            if (!$image) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            ProductImage::where('product_id', $image->product_id)
                ->update(['is_primary' => false]);

            $image->is_primary = true;
            $image->save();

            DB::commit();

            return $this->successResponse($image, 'success.primary_image_set');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Reorder images (vendor's store only)
     */
    public function reorderImages(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'images' => 'required|array',
                'images.*.id' => 'required|integer|exists:product_images,id',
                'images.*.sort_order' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            foreach ($request->images as $imageData) {
                // Verify image belongs to vendor's store product
                $image = ProductImage::whereHas('product', function ($q) use ($storeId) {
                    $q->where('store_id', $storeId);
                })->find($imageData['id']);

                if ($image) {
                    $image->sort_order = $imageData['sort_order'];
                    $image->save();
                }
            }

            DB::commit();

            return $this->successResponse(null, 'success.images_reordered');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Duplicate a product (vendor's store only)
     */
    public function duplicate($id): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $product = Product::with(['images', 'productOptions.productOptionValues', 'addons'])
                ->where('store_id', $storeId)
                ->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            $newProduct = $product->replicate();
            $newProduct->name_en = $product->name_en . ' (Copy)';
            $newProduct->name_ar = $product->name_ar . ' (نسخة)';
            $newProduct->is_active = false;
            $newProduct->view_count = 0;
            $newProduct->sales_count = 0;
            $newProduct->save();

            foreach ($product->images as $image) {
                ProductImage::create([
                    'product_id' => $newProduct->id,
                    'image_path' => $image->image_path,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order,
                ]);
            }

            $newProduct->load(['store', 'category', 'subcategory', 'images']);

            DB::commit();

            return $this->successResponse($newProduct, 'success.product_duplicated', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Reorder products (bulk update sort_order) - vendor's store only
     */
    public function reorderProducts(Request $request): JsonResponse
    {
        try {
            $vendor = auth('vendors')->user();
            $storeId = $vendor->store?->id;

            if (!$storeId) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'products' => 'required|array',
                'products.*.id' => 'required|integer|exists:products,id',
                'products.*.sort_order' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            foreach ($request->products as $productData) {
                // Verify product belongs to vendor's store
                Product::where('id', $productData['id'])
                    ->where('store_id', $storeId)
                    ->update(['sort_order' => $productData['sort_order']]);
            }

            DB::commit();

            return $this->successResponse(null, 'success.products_reordered');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
