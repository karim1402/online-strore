<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class MainCategoryController extends Controller
{
    use ApiResponse;

    /**
     * Get all active main categories (public - no auth required)
     * Returns only id and localized name based on Accept-Language header
     */
    public function getPublicMainCategories(): JsonResponse
    {
        try {
            $mainCategories = MainCategory::active()
                ->ordered()
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name, // This uses the accessor which returns localized name
                    ];
                });

            return $this->successResponse($mainCategories, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
