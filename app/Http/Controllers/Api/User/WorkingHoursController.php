<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class WorkingHoursController extends Controller
{
    use ApiResponse;

    /**
     * Check if the app is currently open.
     * Uses Egypt timezone (Africa/Cairo).
     */
    public function index(): JsonResponse
    {
        $hours = AppSetting::getWorkingHours();
        $now = Carbon::now(AppSetting::TIMEZONE);
        $isOpen = AppSetting::isOpen();

        $messageKey = $isOpen
            ? 'working_hours.service_available'
            : 'working_hours.service_not_available';

        $replace = $isOpen
            ? []
            : ['opening_time' => $hours['opening_time'], 'closing_time' => $hours['closing_time']];

        return $this->successResponse([
            'is_open' => $isOpen,
            'opening_time' => $hours['opening_time'],
            'closing_time' => $hours['closing_time'],
            'current_time' => $now->format('H:i'),
            'timezone' => AppSetting::TIMEZONE,
        ], $messageKey, $replace);
    }
}
