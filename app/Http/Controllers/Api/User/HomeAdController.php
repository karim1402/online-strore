<?php

namespace App\Http\Controllers\Api\User;

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
        $ads = HomeAd::active()->ordered()->get();

        $data = [
            'banners' => $ads->where('type', 'banner')->values(),
            'offers' => $ads->where('type', 'offer')->values(),
        ];

        return $this->successResponse($data);
    }
}
