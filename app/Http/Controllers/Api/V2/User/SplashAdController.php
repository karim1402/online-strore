<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Models\SplashAd;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SplashAdController extends Controller
{
    use ApiResponse;

    /**
     * Get a random active splash ad image.
     */
    public function getRandom(): JsonResponse
    {
        $ad = SplashAd::where('is_active', true)->inRandomOrder()->first();

        if (!$ad) {
            return $this->successResponse(null, 'success.data_retrieved');
        }

        return $this->successResponse($ad, 'success.data_retrieved');
    }
}
