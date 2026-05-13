<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Module;
use App\Models\Product;
use App\Services\LocalizationService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get categories by module ID.
     *
     * @param int $moduleId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByModule($moduleId, Request $request)
    {
        // Validate module exists
        $module = Module::find($moduleId);
        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Module']),
            ], 404);
        }

        // Get active categories for the module
        $categories = Category::where('module_id', $moduleId)
            ->active()
            ->whereNull('parent_id') // Get only top-level categories
            ->orderBy('sort_order', 'asc')
            // ->orderBy('name_en', 'asc')
            ->get();

        // Transform the data
        $categoriesData = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name_en' => $category->name_en,
                'name_ar' => $category->name_ar,
                'description_en' => $category->description_en,
                'description_ar' => $category->description_ar,
                'image_url' => $category->image_url,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
            ];
        });

        // Localize the data
        $localizedCategories = LocalizationService::localizeCollection($categoriesData->toArray(), ['name', 'description']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => $localizedCategories,
        ], 200);
    }
    /**
     * Get a category with its subcategories and products.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::active()
            ->where('id', $id)
            ->whereNull('parent_id') // Ensure it's a main category
            ->with(['children' => function ($query) {
                $query->active()
                    ->with(['products' => function ($q) {
                        $q->active()
                            ->with(['primaryImage', 'store' => function($q) {
                                $q->select('id', 'name_en', 'name_ar', 'status');
                            }])
                            ->withExists('productOptions')
                            ->orderBy('sort_order', 'asc');
                    }])
                    ->orderBy('sort_order', 'asc');
            }, 'products' => function ($query) {
                // Products directly in the main category
                $query->active()
                    ->with(['primaryImage', 'store' => function($q) {
                        $q->select('id', 'name_en', 'name_ar', 'status');
                    }])
                    ->orderBy('sort_order', 'asc');
            }])
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Category']),
            ], 404);
        }

        // Transform the data
        $categoryData = [
            'id' => $category->id,
            'name_en' => $category->name_en,
            'name_ar' => $category->name_ar,
            'description_en' => $category->description_en,
            'description_ar' => $category->description_ar,
            'image_url' => $category->image_url,
            'is_active' => $category->is_active,
            'sort_order' => $category->sort_order,
            'products' => $category->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name_en' => $product->name_en,
                    'name_ar' => $product->name_ar,
                    'base_price' => $product->base_price,
                    'offer_price' => $product->offer_price,
                    'quantity' => $product->quantity,
                    'image_url' => $product->image_url,
                    'has_option_group' => $product->product_options_exists,
                    'store' => $product->store ? [
                        'id' => $product->store->id,
                        'name_en' => $product->store->name_en,
                        'name_ar' => $product->store->name_ar,
                    ] : null,
                ];
            }),
            'subcategories' => $category->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name_en' => $child->name_en,
                    'name_ar' => $child->name_ar,
                    'description_en' => $child->description_en,
                    'description_ar' => $child->description_ar,
                    'image_url' => $child->image_url,
                    'is_active' => $child->is_active,
                    'sort_order' => $child->sort_order,
                    'products' => $child->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name_en' => $product->name_en,
                            'name_ar' => $product->name_ar,
                            'base_price' => $product->base_price,
                            'offer_price' => $product->offer_price,
                            'quantity' => $product->quantity,
                            'image_url' => $product->image_url,
                            'has_option_group' => $product->product_options_exists,
                            'store' => $product->store ? [
                                'id' => $product->store->id,
                                'name_en' => $product->store->name_en,
                                'name_ar' => $product->store->name_ar,
                            ] : null,
                        ];
                    }),
                ];
            }),
        ];

        // Localize the data
        $localizedCategory = LocalizationService::localizeFields($categoryData, ['name', 'description']);
        $localizedCategory['products'] = LocalizationService::localizeCollection($categoryData['products']->toArray(), ['name']);
        $localizedCategory['subcategories'] = $categoryData['subcategories']->map(function ($sub) {
            $localizedSub = LocalizationService::localizeFields($sub, ['name', 'description']);
            $localizedSub['products'] = LocalizationService::localizeCollection($sub['products']->toArray(), ['name']);
            return $localizedSub;
        })->toArray();

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => $localizedCategory,
        ], 200);
    }
    /**
     * Get subcategories with their products for a given parent category.
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubcategories($categoryId)
    {
        // Validate parent category exists
        $parentCategory = Category::active()->find($categoryId);
        if (!$parentCategory) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Category']),
            ], 404);
        }

        // Get subcategories with products (products are linked via subcategory_id)
        $subcategories = Category::active()
            ->where('parent_id', $categoryId)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Load products for each subcategory using subcategory_id
        $subcategories->load(['products' => function ($q) {
            $q->active()
                ->with(['primaryImage'])
                ->withExists('productOptions')
                ->orderBy('sort_order', 'asc');
        }]);

        // Also load products by subcategory_id for subcategories
        foreach ($subcategories as $subcategory) {
            // Get products where subcategory_id matches this subcategory
            $subcategory->setRelation('products', 
                Product::active()
                    ->where('subcategory_id', $subcategory->id)
                    ->with(['primaryImage'])
                    ->withExists('productOptions')
                    ->orderBy('sort_order', 'asc')
                    ->get()
            );
        }

        // Transform the data
        $subcategoriesData = $subcategories->map(function ($subcategory) {
            return [
                'id' => $subcategory->id,
                'name_en' => $subcategory->name_en,
                'name_ar' => $subcategory->name_ar,
                'description_en' => $subcategory->description_en,
                'description_ar' => $subcategory->description_ar,
                'image_url' => $subcategory->image_url,
                'is_active' => $subcategory->is_active,
                'sort_order' => $subcategory->sort_order,
                'products' => $subcategory->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name_en' => $product->name_en,
                        'name_ar' => $product->name_ar,
                        'base_price' => $product->base_price,
                        'offer_price' => $product->offer_price,
                        'quantity' => $product->quantity,
                        'image_url' => $product->image_url,
                        'has_option_group' => $product->product_options_exists,
                    ];
                }),
            ];
        });

        // Localize the data
        $localizedSubcategories = $subcategoriesData->map(function ($sub) {
            $localizedSub = LocalizationService::localizeFields($sub, ['name', 'description']);
            $localizedSub['products'] = LocalizationService::localizeCollection($sub['products']->toArray(), ['name']);
            return $localizedSub;
        });

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'subcategories' => $localizedSubcategories,
                'total' => $subcategoriesData->count(),
            ],
        ], 200);
    }
    /**
     * Get all categories with their related products.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllWithProducts(Request $request,$id)
    {
        $query = Category::active()
            ->whereNull('parent_id')->where('module_id', $id); // Top-level categories


        $categories = $query->with(['products' => function ($q) {
                $q->active()
                    ->with(['primaryImage', 'store' => function($q) {
                        $q->select('id', 'name_en', 'name_ar', 'status');
                    }])
                    ->orderBy('sort_order', 'asc');
            }, 'children' => function ($q) {
                $q->active()->with(['products' => function ($pq) {
                    $pq->active()
                        ->with(['primaryImage', 'store' => function($sq) {
                            $sq->select('id', 'name_en', 'name_ar', 'status');
                        }])
                        ->withExists('productOptions')
                        ->orderBy('sort_order', 'asc');
                }]);
            }])
            ->orderBy('sort_order', 'asc')
            ->get();

        // Transform the data
        $categoriesData = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name_en' => $category->name_en,
                'name_ar' => $category->name_ar,
                'description_en' => $category->description_en,
                'description_ar' => $category->description_ar,
                'image_url' => $category->image_url,
                'sort_order' => $category->sort_order,
                'products' => $category->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name_en' => $product->name_en,
                        'name_ar' => $product->name_ar,
                        'base_price' => $product->base_price,
                        'offer_price' => $product->offer_price,
                        'quantity' => $product->quantity,
                        'image_url' => $product->image_url,
                        'has_option_group' => $product->product_options_exists,
                        'store' => $product->store ? [
                            'id' => $product->store->id,
                            'name_en' => $product->store->name_en,
                            'name_ar' => $product->store->name_ar,
                        ] : null,
                    ];
                }),
                'subcategories' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name_en' => $child->name_en,
                        'name_ar' => $child->name_ar,
                        'description_en' => $child->description_en,
                        'description_ar' => $child->description_ar,
                        'image_url' => $child->image_url,
                        'sort_order' => $child->sort_order,
                        'products' => $child->products->map(function ($product) {
                            return [
                                'id' => $product->id,
                                'name_en' => $product->name_en,
                                'name_ar' => $product->name_ar,
                                'base_price' => $product->base_price,
                                'offer_price' => $product->offer_price,
                                'quantity' => $product->quantity,
                                'image_url' => $product->image_url,
                                'has_option_group' => $product->product_options_exists,
                                'store' => $product->store ? [
                                    'id' => $product->store->id,
                                    'name_en' => $product->store->name_en,
                                    'name_ar' => $product->store->name_ar,
                                ] : null,
                            ];
                        }),
                    ];
                }),
            ];
        });

        // Localize the data
        $localizedCategories = $categoriesData->map(function ($cat) {
            $localizedCat = LocalizationService::localizeFields($cat, ['name', 'description']);
            $localizedCat['products'] = LocalizationService::localizeCollection($cat['products']->toArray(), ['name']);
            $localizedCat['subcategories'] = $cat['subcategories']->map(function ($sub) {
                $localizedSub = LocalizationService::localizeFields($sub, ['name', 'description']);
                $localizedSub['products'] = LocalizationService::localizeCollection($sub['products']->toArray(), ['name']);
                return $localizedSub;
            })->toArray();
            return $localizedCat;
        });

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'categories' => $localizedCategories,
                'total' => $categoriesData->count(),
            ],
        ], 200);
    }


    
}
