<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\LocalizationService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Search products using database queries.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 15);
        
        $productsQuery =  Product::where('id' ,'!=' , 74)->with([
            'primaryImage',
            'category' => function ($query) {
                $query->select('id', 'name_en', 'name_ar');
            }
        ])->withExists('productOptions')->active();

        // Apply keyword search
        if (!empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name_en', 'like', "%{$query}%")
                  ->orWhere('name_ar', 'like', "%{$query}%")
                  ->orWhere('description_en', 'like', "%{$query}%")
                  ->orWhere('description_ar', 'like', "%{$query}%")
                  ->orWhere('search_keywords', 'like', "%{$query}%");
            });
        }

        // Apply filters
        if ($request->filled('category_id')) {
            $productsQuery->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('module_id')) {
            $productsQuery->whereHas('category', function ($q) use ($request) {
                $q->where('module_id', $request->input('module_id'));
            });
        }

        if ($request->filled('store_id')) {
            $productsQuery->where('store_id', $request->input('store_id'));
        }

        if ($request->filled('min_price')) {
            $productsQuery->where('base_price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $productsQuery->where('base_price', '<=', $request->input('max_price'));
        }

        // Sort results
        // For database search, we don't have relevance scoring, so we sort by sort_order or created_at
        $productsQuery->orderBy('sort_order', 'asc')
                      ->orderBy('created_at', 'desc');

        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

        // Transform the data
        $productsData = collect($products->items())->map(function ($product) {
            return [
                'id' => $product->id,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar,
                'name_ar' => $product->name_ar,
                'base_price' => $product->base_price,
                'offer_price' => $product->offer_price,
                'quantity' => $product->quantity,
                'has_option_group' => $product->product_options_exists,
                'image' => $product->primaryImage ? $product->primaryImage->image_url : null,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name_en' => $product->category->name_en,
                    'name_ar' => $product->category->name_ar,
                ] : null,
            ];
        })->toArray();

        // Localize the data
        $localizedProducts = LocalizationService::localizeCollection($productsData, ['name']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'products' => $localizedProducts,
                'total' => $products->total(),
                'page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
            ],
        ], 200);
    }
    /**
     * Get best seller products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bestSellers(Request $request)
    {
        $products = Product::where('id', '!=', 74)
            ->with(['primaryImage'])
            ->withExists('productOptions')
            ->active()
            ->where('is_best_seller', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $productsData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar,
                'name_ar' => $product->name_ar,
                'base_price' => $product->base_price,
                'offer_price' => $product->offer_price,
                'quantity' => $product->quantity,
                'has_option_group' => $product->product_options_exists,
                'image' => $product->best_seller_image_url ?: ($product->primaryImage ? $product->primaryImage->image_url : null),
            ];
        })->toArray();

        $localizedProducts = LocalizationService::localizeCollection($productsData, ['name']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'products' => $localizedProducts,
                'count' => count($localizedProducts),
            ],
        ], 200);
    }

    /**
     * Get random products with basic information.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function random(Request $request)
    {
        // Get count from request, default to 4
        $count = $request->input('count', 4);
        $count = min(max((int)$count, 1), 20); // Between 1 and 20

        // Get random active products from approved stores
        $products = Product::where('id','!=',74)->
        with([
            'primaryImage',
            // 'store' => function ($query) {
            //     $query->select('id', 'status');
            // }
        ])
        ->withExists('productOptions')
        ->active()
        // ->whereHas('store', function ($query) {
        //     $query->where('status', 'approved');
        // })
        ->inRandomOrder()
        ->limit($count)
        ->get();

        // Transform the data
        $productsData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name_en' => $product->name_en,
                'name_ar' => $product->name_ar,
                'base_price' => $product->base_price,
                'offer_price' => $product->offer_price,
                'quantity' => $product->quantity,
                'has_option_group' => $product->product_options_exists,
                'image_url' => $product->primaryImage ? $product->primaryImage->image_url : null,
            ];
        })->toArray();

        // Localize the data
        $localizedProducts = LocalizationService::localizeCollection($productsData, ['name']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'products' => $localizedProducts,
                'count' => count($localizedProducts),
            ],
        ], 200);
    }

    /**
     * Get complete product details including images, addons, and options.
     *
     * @param int $productId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($productId, Request $request)
    {
        // Find the product with all relationships
        $product = Product::with([
            // 'store' => function ($query) {
            //     $query->select('id', 'name_en', 'name_ar', 'description_en', 'description_ar', 'logo', 'status');
            // },
            'category' => function ($query) {
                $query->select('id', 'name_en', 'name_ar', 'description_en', 'description_ar', 'image', 'is_active');
            },
            'images' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc');
            },
            'addons' => function ($query) {
                $query->where('addons.is_active', true)
                    ->orderBy('product_addons.sort_order', 'asc')
                    ->orderBy('addons.name_en', 'asc');
            },
            'productOptions' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'productOptions.optionGroup' => function ($query) {
                $query->where('is_active', true);
            },
            'productOptions.productOptionValues.optionValue' => function ($query) {
                $query->where('is_active', true);
            }
        ])
        ->active()
        ->find($productId);

        // Check if product exists
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Product']),
            ], 404);
        }

        // Check if store is approved
        // if (!$product->store || !$product->store->isApproved()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Product']),
        //     ], 404);
        // }

        // Increment view count
        $product->incrementViewCount();

        // Transform product data
        $productData = [
            'id' => $product->id,
            'name_en' => $product->name_en,
            'name_ar' => $product->name_ar,
            'description_en' => $product->description_en,
            'description_ar' => $product->description_ar,
            // 'search_keywords' => $product->search_keywords,
            'base_price' => $product->base_price,
            'offer_price' => $product->offer_price,
            'quantity' => $product->quantity,
            'is_active' => $product->is_active,
            'view_count' => $product->view_count,
            'sales_count' => $product->sales_count,
            'sort_order' => $product->sort_order,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];

        // Add store information
        // $productData['store'] = [
        //     'id' => $product->store->id,
        //     'name_en' => $product->store->name_en,
        //     'name_ar' => $product->store->name_ar,
        //     'description_en' => $product->store->description_en,
        //     'description_ar' => $product->store->description_ar,
        //     'logo_url' => $product->store->logo_url,
        //     'status' => $product->store->status,
        // ];

        // Add category information
        if ($product->category) {
            $productData['category'] = [
                'id' => $product->category->id,
                'name_en' => $product->category->name_en,
                'name_ar' => $product->category->name_ar,
                'description_en' => $product->category->description_en,
                'description_ar' => $product->category->description_ar,
                'image_url' => $product->category->image_url,
                'is_active' => $product->category->is_active,
            ];
        }

        // Add images
        $productData['images'] = $product->images->map(function ($image) {
            return [
                'id' => $image->id,
                'image_url' => $image->image_url,
                'is_primary' => $image->is_primary,
                'sort_order' => $image->sort_order,
            ];
        })->toArray();

        // Add primary image separately for convenience
        $primaryImage = $product->images->where('is_primary', true)->first();
        $productData['primary_image'] = $primaryImage ? [
            'id' => $primaryImage->id,
            'image_url' => $primaryImage->image_url,
        ] : null;

        // Add addons
        $productData['addons'] = $product->addons->map(function ($addon) {
            return [
                'id' => $addon->id,
                'name_en' => $addon->name_en,
                'name_ar' => $addon->name_ar,
                'description_en' => $addon->description_en,
                'description_ar' => $addon->description_ar,
                'price' => $addon->price,
                'addon_category' => $addon->addon_category,
                'is_active' => $addon->is_active,
                'is_available' => $addon->pivot->is_available,
                'sort_order' => $addon->pivot->sort_order,
            ];
        })->toArray();

        // Add options with their values
        $productData['options'] = $product->productOptions->map(function ($productOption) use ($product) {
            // Skip if option group is not active
            if (!$productOption->optionGroup) {
                return null;
            }

            return [
                'id' => $productOption->id,
                'is_required' => $productOption->is_required,
                'sort_order' => $productOption->sort_order,
                'option_group' => [
                    'id' => $productOption->optionGroup->id,
                    'name_en' => $productOption->optionGroup->name_en,
                    'name_ar' => $productOption->optionGroup->name_ar,
                    'type' => $productOption->optionGroup->type,
                    'is_active' => $productOption->optionGroup->is_active,
                ],
                'values' => $productOption->productOptionValues
                    ->filter(function ($productOptionValue) {
                        // Only include active option values
                        return $productOptionValue->optionValue && $productOptionValue->is_available;
                    })
                    ->map(function ($productOptionValue) use ($product) {
                        $optionValue = $productOptionValue->optionValue;
                        
                        return [
                            'id' => $productOptionValue->id,
                            'option_value_id' => $optionValue->id,
                            'value_en' => $optionValue->value_en,
                            'value_ar' => $optionValue->value_ar,
                            'price_type' => $productOptionValue->price_type,
                            'price_value' => $productOptionValue->price_value,
                            'calculated_price' => $productOptionValue->calculatePrice($product->base_price),
                            'stock_quantity' => $productOptionValue->stock_quantity,
                            'is_available' => $productOptionValue->is_available,
                            'in_stock' => $productOptionValue->inStock(),
                        ];
                    })
                    ->values()
                    ->toArray(),
            ];
        })->filter()->values()->toArray(); // Remove null entries and reindex

        // Localize all nested data (store, category, addons, options, values)
        $localizedProduct = LocalizationService::localizeCollection([$productData], ['name', 'description', 'value'])[0];

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'product' => $localizedProduct,
            ],
        ], 200);
    }
    /**
     * Get Makook Sandwich product details (ID 74) including images for options.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMakookSandwich(Request $request)
    {
        $productId = 74;

        // Find the product with all relationships
        $product = Product::with([
            'category' => function ($query) {
                $query->select('id', 'name_en', 'name_ar', 'description_en', 'description_ar', 'image', 'is_active');
            },
            'images' => function ($query) {
                $query->orderBy('is_primary', 'desc')->orderBy('sort_order', 'asc');
            },
            'addons' => function ($query) {
                $query->where('addons.is_active', true)
                    ->orderBy('product_addons.sort_order', 'asc')
                    ->orderBy('addons.name_en', 'asc');
            },
            'productOptions' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            },
            'productOptions.optionGroup' => function ($query) {
                $query->where('is_active', true);
            },
            'productOptions.productOptionValues.optionValue' => function ($query) {
                $query->where('is_active', true);
            }
        ])
        ->active()
        ->find($productId);

        // Check if product exists
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Product']),
            ], 404);
        }

        // Increment view count
        $product->incrementViewCount();

        // Transform product data
        $productData = [
            'id' => $product->id,
            'name_en' => $product->name_en,
            'name_ar' => $product->name_ar,
            'description_en' => $product->description_en,
            'description_ar' => $product->description_ar,
            'base_price' => $product->base_price,
            'offer_price' => $product->offer_price,
            'quantity' => $product->quantity,
            'is_active' => $product->is_active,
            'view_count' => $product->view_count,
            'sales_count' => $product->sales_count,
            'sort_order' => $product->sort_order,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at,
        ];

        // Add category information
        if ($product->category) {
            $productData['category'] = [
                'id' => $product->category->id,
                'name_en' => $product->category->name_en,
                'name_ar' => $product->category->name_ar,
                'description_en' => $product->category->description_en,
                'description_ar' => $product->category->description_ar,
                'image_url' => $product->category->image_url,
                'is_active' => $product->category->is_active,
            ];
        }

        // Add images
        $productData['images'] = $product->images->map(function ($image) {
            return [
                'id' => $image->id,
                'image_url' => $image->image_url,
                'is_primary' => $image->is_primary,
                'sort_order' => $image->sort_order,
            ];
        })->toArray();

        // Add primary image separately for convenience
        $primaryImage = $product->images->where('is_primary', true)->first();
        $productData['primary_image'] = $primaryImage ? [
            'id' => $primaryImage->id,
            'image_url' => $primaryImage->image_url,
        ] : null;

        // Add addons
        $productData['addons'] = $product->addons->map(function ($addon) {
            return [
                'id' => $addon->id,
                'name_en' => $addon->name_en,
                'name_ar' => $addon->name_ar,
                'description_en' => $addon->description_en,
                'description_ar' => $addon->description_ar,
                'price' => $addon->price,
                'addon_category' => $addon->addon_category,
                'is_active' => $addon->is_active,
                'is_available' => $addon->pivot->is_available,
                'sort_order' => $addon->pivot->sort_order,
            ];
        })->toArray();

        // Add options with their values
        $productData['options'] = $product->productOptions->map(function ($productOption) use ($product) {
            // Skip if option group is not active
            if (!$productOption->optionGroup) {
                return null;
            }

            return [
                'id' => $productOption->id,
                'is_required' => $productOption->is_required,
                'sort_order' => $productOption->sort_order,
                'option_group' => [
                    'id' => $productOption->optionGroup->id,
                    'name_en' => $productOption->optionGroup->name_en,
                    'name_ar' => $productOption->optionGroup->name_ar,
                    'type' => $productOption->optionGroup->type,
                    'is_active' => $productOption->optionGroup->is_active,
                    'image' => $productOption->optionGroup->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($productOption->optionGroup->image) : null,
                ],
                'values' => $productOption->productOptionValues
                    ->filter(function ($productOptionValue) {
                        // Only include active option values
                        return $productOptionValue->optionValue && $productOptionValue->is_available;
                    })
                    ->map(function ($productOptionValue) use ($product) {
                        $optionValue = $productOptionValue->optionValue;
                        
                        return [
                            'id' => $productOptionValue->id,
                            'option_value_id' => $optionValue->id,
                            'value_en' => $optionValue->value_en,
                            'value_ar' => $optionValue->value_ar,
                            'price_type' => $productOptionValue->price_type,
                            'price_value' => $productOptionValue->price_value,
                            'calculated_price' => $productOptionValue->calculatePrice($product->base_price),
                            'stock_quantity' => $productOptionValue->stock_quantity,
                            'is_available' => $productOptionValue->is_available,
                            'in_stock' => $productOptionValue->inStock(),
                            'image_url' => $optionValue->image_url,
                        ];
                    })
                    ->values()
                    ->toArray(),
            ];
        })->filter()->values()->toArray(); // Remove null entries and reindex

        // Localize all nested data (store, category, addons, options, values)
        $localizedProduct = LocalizationService::localizeCollection([$productData], ['name', 'description', 'value'])[0];

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'product' => $localizedProduct,
            ],
        ], 200);
    }
}
