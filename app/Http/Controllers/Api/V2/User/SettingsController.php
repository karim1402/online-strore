<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * Get application settings/keys
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Settings retrieved successfully',
            'data' => [
                'public_key'    => config('services.client_app.public_key'),
                'client_secret' => config('services.client_app.client_secret'),
            ]
        ]);
    }

    /**
     * Get app version info for both platforms (public, no auth required).
     * GET /user/app-version
     */
    public function appVersion(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'App version retrieved successfully',
            'data' => [
                'android' => AppSetting::getAppVersion('android'),
                'ios'     => AppSetting::getAppVersion('ios'),
            ],
        ]);
    }
}
