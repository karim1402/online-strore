<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsMisrService
{
    /**
     * Send OTP via SMS MISR API.
     *
     * @param string $phone
     * @param string $otp
     * @return bool
     */
    public static function sendOtp(string $phone, string $otp): bool
    {
        try {
            $response = Http::post('https://smsmisr.com/api/SMS/', [
                'environment' => 2,
                'username' => config('services.smsmisr.username'),
                'password' => config('services.smsmisr.password'),
                'sender' => config('services.smsmisr.sender'),
                'mobile' => "+201207048631",
                "language" => 2,
                "message" => "مرحبا بك في تطبيق مكوك",
                // 'template' => config('services.smsmisr.template'),
                // 'otp' => $otp
            ]);

            if ($response->successful()) {
                // The API usually returns `code` 1901 when successful
                // We will just return true if HTTP 2xx
                Log::info('SMS MISR Response: ' . $response->body());
                return true;
            }

            Log::error('SMS MISR failed to send OTP: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Exception in SmsMisrService: ' . $e->getMessage());
            return false;
        }
    }
}
