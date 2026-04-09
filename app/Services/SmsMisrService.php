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
            $response = Http::post('https://smsmisr.com/api/OTP/', [
                'environment' => 2,
                'username' => '34144164-6497-41a0-acd4-d982c40d45f5',// config('services.smsmisr.username'),
                'password' => "43bf84fc100733e73f3a000a5e0cf6b9bf3d69f4eae91f6b07634d74b0d2e361",//config('services.smsmisr.password'),
                'sender' => 'b611afb996655a94c8e942a823f1421de42bf8335d24ba1f84c437b2ab11ca27', //config('services.smsmisr.sender'),
                'mobile' => "01024357231",
                'template' => "0f9217c9d760c1c0ed47b8afb5425708da7d98729016a8accfc14f9cc8d1ba83",
                'otp' => $otp
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
