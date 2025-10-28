<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Services\LocalizationService;
use Illuminate\Http\Request;

class MainCategoryController extends Controller
{
    /**
     * Get all active main categories.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get all active main categories ordered by sort_order
        $mainCategories = MainCategory::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('name_en', 'asc')
            ->get();

        // Transform the data
        $categoriesData = $mainCategories->map(function ($category) {
            return [
                'id' => $category->id,
                'name_en' => $category->name_en,
                'name_ar' => $category->name_ar,
                'description_en' => $category->description_en,
                'description_ar' => $category->description_ar,
                'image_url' => $category->image_url,
                'status' => $category->status,
                'sort_order' => $category->sort_order,
            ];
        });

        // Localize the data
        $localizedCategories = LocalizationService::localizeCollection($categoriesData->toArray(), ['name', 'description']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'main_categories' => $localizedCategories,
                'total' => $categoriesData->count(),
            ],
        ], 200);
    }

    /**
     * Get a single main category by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Find the main category
        $mainCategory = MainCategory::active()
            ->where('id', $id)
            ->first();

        if (!$mainCategory) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.category_not_found'),
            ], 404);
        }

        // Transform the data
        $categoryData = [
            'id' => $mainCategory->id,
            'name_en' => $mainCategory->name_en,
            'name_ar' => $mainCategory->name_ar,
            'description_en' => $mainCategory->description_en,
            'description_ar' => $mainCategory->description_ar,
            'image_url' => $mainCategory->image_url,
            'status' => $mainCategory->status,
            'sort_order' => $mainCategory->sort_order,
        ];

        // Localize the data
        $localizedCategory = LocalizationService::localizeFields($categoryData, ['name', 'description']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => $localizedCategory,
        ], 200);
    }
}
