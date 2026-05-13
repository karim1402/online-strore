<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Traits\ApiResponse;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AppSettingController extends Controller
{
    use ApiResponse;

    /**
     * Get the current working hours.
     */
    public function getWorkingHours(): JsonResponse
    {
        $hours = AppSetting::getWorkingHours();
        $now = Carbon::now(AppSetting::TIMEZONE);

        return $this->successResponse([
            'opening_time' => $hours['opening_time'],
            'closing_time' => $hours['closing_time'],
            'is_open' => AppSetting::isOpen(),
            'current_time' => $now->format('H:i'),
            'timezone' => AppSetting::TIMEZONE,
        ], 'success.operation_successful');
    }

    /**
     * Update the working hours.
     */
    public function updateWorkingHours(Request $request): JsonResponse
    {
        $rules = [
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
        ];

        $validator = ValidationService::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        AppSetting::set('opening_time', $request->opening_time);
        AppSetting::set('closing_time', $request->closing_time);

        $now = Carbon::now(AppSetting::TIMEZONE);

        return $this->successResponse([
            'opening_time' => $request->opening_time,
            'closing_time' => $request->closing_time,
            'is_open' => AppSetting::isOpen(),
            'current_time' => $now->format('H:i'),
            'timezone' => AppSetting::TIMEZONE,
        ], 'success.operation_successful');
    }

    /**
     * Get app version settings for a given platform.
     * GET /admin/app-settings/app-version/{platform}
     */
    public function getAppVersion(string $platform): JsonResponse
    {
        $platform = strtolower($platform);
        if (!in_array($platform, ['android', 'ios'])) {
            return $this->errorResponse('Platform must be android or ios.', 422);
        }

        return $this->successResponse([
            $platform => AppSetting::getAppVersion($platform),
        ], 'success.operation_successful');
    }

    /**
     * Update app version settings for a given platform.
     * PUT /admin/app-settings/app-version/{platform}
     */
    public function updateAppVersion(Request $request, string $platform): JsonResponse
    {
        $platform = strtolower($platform);
        if (!in_array($platform, ['android', 'ios'])) {
            return $this->errorResponse('Platform must be android or ios.', 422);
        }

        $rules = [
            'minimum_version'         => 'sometimes|string|max:20',
            'latest_version'          => 'sometimes|string|max:20',
            'force_update_message'    => 'sometimes|string|max:500',
            'optional_update_message' => 'sometimes|string|max:500',
        ];

        $validator = ValidationService::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        AppSetting::setAppVersion($platform, $request->only(array_keys($rules)));

        return $this->successResponse([
            $platform => AppSetting::getAppVersion($platform),
        ], 'success.operation_successful');
    }

    /**
     * Get delivery settings.
     * GET /admin/app-settings/delivery-settings
     */
    public function getDeliverySettings(): JsonResponse
    {
        return $this->successResponse([
            'delivery_base_fee'  => AppSetting::get('delivery_base_fee', '0'),
            'delivery_km_fee'    => AppSetting::get('delivery_km_fee', '0'),
            'delivery_start_lat' => AppSetting::get('delivery_start_lat', ''),
            'delivery_start_lng' => AppSetting::get('delivery_start_lng', ''),
        ], 'success.operation_successful');
    }

    /**
     * Update delivery settings.
     * PUT /admin/app-settings/delivery-settings
     */
    public function updateDeliverySettings(Request $request): JsonResponse
    {
        $rules = [
            'delivery_base_fee'  => 'required|numeric|min:0',
            'delivery_km_fee'    => 'required|numeric|min:0',
            'delivery_start_lat' => 'required|numeric|between:-90,90',
            'delivery_start_lng' => 'required|numeric|between:-180,180',
        ];

        $validator = ValidationService::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        AppSetting::set('delivery_base_fee', $request->delivery_base_fee);
        AppSetting::set('delivery_km_fee', $request->delivery_km_fee);
        AppSetting::set('delivery_start_lat', $request->delivery_start_lat);
        AppSetting::set('delivery_start_lng', $request->delivery_start_lng);

        return $this->successResponse([
            'delivery_base_fee'  => AppSetting::get('delivery_base_fee'),
            'delivery_km_fee'    => AppSetting::get('delivery_km_fee'),
            'delivery_start_lat' => AppSetting::get('delivery_start_lat'),
            'delivery_start_lng' => AppSetting::get('delivery_start_lng'),
        ], 'success.operation_successful');
    }
}
