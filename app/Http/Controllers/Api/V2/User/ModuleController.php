<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ModuleController extends Controller
{
    /**
     * Get all active modules.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get all active modules ordered by sort_order
        $modules = Module::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('name_en', 'asc')
            ->get();

        // Transform the data
        $modulesData = $modules->map(function ($module) {
            return [
                'id' => $module->id,
                'name_en' => $module->name_en,
                'name_ar' => $module->name_ar,
                'description_en' => $module->description_en,
                'description_ar' => $module->description_ar,
                'image_url' => (App::getLocale() === 'ar' && ($module->image_ar_v2 ?? $module->image_ar)) ? ($module->image_ar_v2_url ?? $module->image_ar_url) : ($module->image_v2_url ?? $module->image_url),
                'status' => $module->status,
                'sort_order' => $module->sort_order,
            ];
        });

        // Localize the data
        $localizedModules = LocalizationService::localizeCollection($modulesData->toArray(), ['name', 'description']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'modules' => $localizedModules,
                'total' => $modulesData->count(),
            ],
        ], 200);
    }

    /**
     * Get a single module by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Find the module
        $module = Module::active()
            ->where('id', $id)
            ->first();

        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.module_not_found'),
            ], 404);
        }

        // Transform the data
        $moduleData = [
            'id' => $module->id,
            'name_en' => $module->name_en,
            'name_ar' => $module->name_ar,
            'description_en' => $module->description_en,
            'description_ar' => $module->description_ar,
            'image_url' => (App::getLocale() === 'ar' && ($module->image_ar_v2 ?? $module->image_ar)) ? ($module->image_ar_v2_url ?? $module->image_ar_url) : ($module->image_v2_url ?? $module->image_url),
            'status' => $module->status,
            'sort_order' => $module->sort_order,
        ];

        // Localize the data
        $localizedModule = LocalizationService::localizeFields($moduleData, ['name', 'description']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => $localizedModule,
        ], 200);
    }
}
