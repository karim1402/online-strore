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
                'environment' => 1,
                'username' => '34144164-6497-41a0-acd4-d982c40d45f5',// config('services.smsmisr.username'),
                'password' => "5a308f6040662a0243846d345b0a1d5c9d5a3a115ef76f93f1a0a9ce3f710654",//config('services.smsmisr.password'),
                'sender' => 'b4dc28584837651e020f9916cc9fa682353fe86205478f8570e4695fe884d4d8', //config('services.smsmisr.sender'),
                'mobile' => $phone,
                'template' => "e83faf6025ec41d0f40256d2812629f5fa9291d05c8322f31eea834302501da8",
                'otp' => $otp
            ]);

            // dd($response->body());

            

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
