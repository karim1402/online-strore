<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Models\HomeAd;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class HomeAdController extends Controller
{
    use ApiResponse;

    /**
     * Get all home ads grouped by type.
     */
    public function index(): JsonResponse
    {
        $ads = HomeAd::active()->ordered()->get()->map(fn($ad) => $this->transformAd($ad));

        $data = [
            'banners' => $ads->where('type', 'banner')->values(),
            'offers' => $ads->where('type', 'offer')->values(),
        ];

        return $this->successResponse($data);
    }

    /**
     * Get products associated with a home ad.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProducts($id): JsonResponse
    {
        $ad = HomeAd::with(['products' => function($q) {
            $q->active()
              ->with([
                  'primaryImage', 
                  'category' => function ($query) {
                      $query->select('id', 'name_en', 'name_ar');
                  }
              ])
              ->withExists('productOptions');
        }])->active()->find($id);

        if (!$ad) {
            return $this->errorResponse('errors.not_found', ['resource' => 'Home Ad'], 404);
        }

        // Transform the data
        $productsData = $ad->products->map(function ($product) {
            return [
                'id' => $product->id,
                'name_en' => $product->name_en,
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
        $localizedProducts = \App\Services\LocalizationService::localizeCollection($productsData, ['name']);

        return $this->successResponse([
            'products' => $localizedProducts,
            'count' => count($localizedProducts),
        ]);
    }

    private function transformAd(HomeAd $ad): array
    {
        $data = $ad->toArray();
        $data['image_url'] = (app()->getLocale() === 'ar' && ($ad->image_ar_v2 ?? $ad->image_ar)) ? ($ad->image_ar_v2_url ?? $ad->image_ar_url) : ($ad->image_v2_url ?? $ad->image_url);
        $data['image_ar_url'] = $ad->image_ar_v2_url ?? $ad->image_ar_url;
        return $data;
    }
}
