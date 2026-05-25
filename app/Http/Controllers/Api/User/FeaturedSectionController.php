<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\FeaturedSection;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FeaturedSectionController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $sections = FeaturedSection::active()
            ->ordered()
            ->get()
            ->map(fn($section) => [
                'id' => $section->id,
                'type' => $section->type,
                'item_id' => $section->item_id,
                'name_en' => $section->name_en,
                'name_ar' => $section->name_ar,
                'name' => $section->name,
                'image_url' => $section->image_url,
                'sort_order' => $section->sort_order,
            ]);

        return $this->successResponse($sections);
    }
}
