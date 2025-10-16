<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\Store;
use App\Models\Category;
use App\Models\OptionGroup;
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
     * Get all products with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['store:id,name_en,name_ar', 'category:id,name_en,name_ar', 'images']);

            // Filter by store
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

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
     * Get a single product by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with([
                'store:id,name_en,name_ar',
                'category:id,name_en,name_ar',
                'images',
                'productOptions.optionGroup',
                'productOptions.productOptionValues.optionValue',
                'addons'
            ])->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            // Increment view count
            $product->incrementViewCount();

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
            $validator = ValidationService::make($request->all(), [
                'store_id' => 'required|integer|exists:stores,id',
                'category_id' => 'required|integer|exists:categories,id',
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'search_keywords' => 'nullable|string',
                'base_price' => 'required|numeric|min:0',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
                'metadata' => 'nullable|array',
                'images' => 'nullable|array',
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

            // Verify store exists
            $store = Store::find($request->store_id);
            if (!$store) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            // Verify category belongs to store
            $category = Category::where('id', $request->category_id)
                ->where('store_id', $request->store_id)
                ->first();
            
            if (!$category) {
                return $this->errorResponse('errors.category_not_found_in_store', [], 404);
            }

            // Create product
            $product = Product::create([
                'store_id' => $request->store_id,
                'category_id' => $request->category_id,
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
                    // Check if option group already assigned
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
                            // Check if option value belongs to this option group
                            $valueExists = OptionValue::where('id', $optionValue['option_value_id'])
                                ->where('option_group_id', $optionGroup['option_group_id'])
                                ->exists();

                            if ($valueExists) {
                                // Check if not already assigned
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

            // Handle addons assignment
            if ($request->filled('addon_ids')) {
                $product->addons()->sync($request->addon_ids);
            }

            $product->load(['store', 'category', 'images', 'productOptions.optionGroup', 'productOptions.productOptionValues.optionValue', 'addons']);

            DB::commit();

            return $this->successResponse($product, 'success.product_created', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update a product
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'category_id' => 'nullable|integer|exists:categories,id',
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

            // Verify category belongs to product's store if changing category
            if ($request->filled('category_id')) {
                $category = Category::where('id', $request->category_id)
                    ->where('store_id', $product->store_id)
                    ->first();
                
                if (!$category) {
                    return $this->errorResponse('errors.category_not_found_in_store', [], 404);
                }
                $product->category_id = $request->category_id;
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
                    // Get current option group IDs
                    $newOptionGroupIds = collect($request->option_groups)->pluck('option_group_id')->toArray();
                    
                    // Remove option groups that are not in the new list
                    ProductOption::where('product_id', $product->id)
                        ->whereNotIn('option_group_id', $newOptionGroupIds)
                        ->delete();

                    // Update or create option groups
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

                        // Handle option values if provided
                        if (isset($optionGroup['option_values']) && is_array($optionGroup['option_values'])) {
                            // Get new option value IDs
                            $newOptionValueIds = collect($optionGroup['option_values'])->pluck('option_value_id')->toArray();
                            
                            // Remove option values that are not in the new list
                            ProductOptionValue::where('product_option_id', $productOption->id)
                                ->whereNotIn('option_value_id', $newOptionValueIds)
                                ->delete();

                            // Update or create option values
                            foreach ($optionGroup['option_values'] as $optionValue) {
                                // Verify option value belongs to this option group
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
                    // If empty array provided, remove all option groups
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

            $product->load(['store', 'category', 'images', 'productOptions.optionGroup', 'productOptions.productOptionValues.optionValue', 'addons']);

            DB::commit();

            return $this->successResponse($product, 'success.product_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a product (soft delete)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Soft delete the product
            $product->delete();

            DB::commit();

            return $this->successResponse(null, 'success.product_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle product active status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $product->is_active = !$product->is_active;
            $product->save();
            $product->load(['store', 'category', 'images']);

            return $this->successResponse($product, 'success.status_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Upload product images
     */
    public function uploadImages(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::find($id);

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
     * Delete a product image
     */
    public function deleteImage($id): JsonResponse
    {
        try {
            $image = ProductImage::find($id);

            if (!$image) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            $wasPrimary = $image->is_primary;
            $productId = $image->product_id;

            // Delete image file
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            $image->delete();

            // If deleted image was primary, set first remaining image as primary
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
     * Set primary image
     */
    public function setPrimaryImage($id): JsonResponse
    {
        try {
            $image = ProductImage::find($id);

            if (!$image) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Remove primary flag from other images
            ProductImage::where('product_id', $image->product_id)
                ->update(['is_primary' => false]);

            // Set this image as primary
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
     * Reorder images
     */
    public function reorderImages(Request $request): JsonResponse
    {
        try {
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
                ProductImage::where('id', $imageData['id'])
                    ->update(['sort_order' => $imageData['sort_order']]);
            }

            DB::commit();

            return $this->successResponse(null, 'success.images_reordered');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Duplicate a product
     */
    public function duplicate($id): JsonResponse
    {
        try {
            $product = Product::with(['images', 'productOptions.productOptionValues', 'addons'])->find($id);

            if (!$product) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Create new product
            $newProduct = $product->replicate();
            $newProduct->name_en = $product->name_en . ' (Copy)';
            $newProduct->name_ar = $product->name_ar . ' (نسخة)';
            $newProduct->is_active = false;
            $newProduct->view_count = 0;
            $newProduct->sales_count = 0;
            $newProduct->save();

            // Copy images (reference same files, don't duplicate)
            foreach ($product->images as $image) {
                ProductImage::create([
                    'product_id' => $newProduct->id,
                    'image_path' => $image->image_path,
                    'is_primary' => $image->is_primary,
                    'sort_order' => $image->sort_order,
                ]);
            }

            $newProduct->load(['store', 'category', 'images']);

            DB::commit();

            return $this->successResponse($newProduct, 'success.product_duplicated', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Reorder products (bulk update sort_order)
     */
    public function reorderProducts(Request $request): JsonResponse
    {
        try {
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
                Product::where('id', $productData['id'])
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
