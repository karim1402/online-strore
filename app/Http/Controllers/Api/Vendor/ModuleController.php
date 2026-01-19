<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    use ApiResponse;

    /**
     * Get all active modules (public - no auth required)
     * Returns only id and localized name based on Accept-Language header
     */
    public function getPublicModules(): JsonResponse
    {
        try {
            $modules = Module::active()
                ->ordered()
                ->get()
                ->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name, // This uses the accessor which returns localized name
                    ];
                });

            return $this->successResponse($modules, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
