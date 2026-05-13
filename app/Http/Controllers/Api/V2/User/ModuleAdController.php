<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\ModuleAd;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ModuleAdController extends Controller
{
    use ApiResponse;

    /**
     * Get active ads for a specific module.
     */
    public function getByModule(int $moduleId): JsonResponse
    {
        try {
            $ads = ModuleAd::with(['product'])
                ->where('module_id', $moduleId)
                ->active()
                ->scheduled()
                ->ordered()
                ->get();

            return $this->successResponse($ads, 'success.module_ads_retrieved');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
