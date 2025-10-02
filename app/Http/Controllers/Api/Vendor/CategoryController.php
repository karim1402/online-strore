<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Get all active main categories (id and name only)
     * Name is returned based on Accept-Language header
     */
    public function index(): JsonResponse
    {
        $categories = MainCategory::where('status', true)
            ->select('id', 'name_en', 'name_ar')
            ->orderBy('name_en')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name, // Uses the accessor based on current locale
                ];
            });

        return $this->successResponse([
            'categories' => $categories
        ], 'success.categories_fetched');
    }
}
