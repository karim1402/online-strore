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
}
