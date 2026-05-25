<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Mail\PasswordResetMail;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ForgotPasswordController extends Controller
{
    use ApiResponse;

    public function sendResetCode(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Resolve the phone: try as-is first, then without leading "2"
        $phone = $request->phone;
        $user = User::where('phone', $phone)->first();

        if (!$user && str_starts_with($phone, '2')) {
            $phoneWithout2 = substr($phone, 1);
            $user = User::where('phone', $phoneWithout2)->first();
            if ($user) {
                $phone = $phoneWithout2;
            }
        }

        if (!$user) {
            return $this->errorResponse('errors.phone_not_found', [], 404);
        }

        // Generate 4-digit OTP
        $code = rand(1000, 9999);

        // Store or update in phone_verifications table using the resolved phone
        DB::table('phone_verifications')->updateOrInsert(
            ['phone' => $phone],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(15),
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]
        );

        // Send OTP via SMS (use original request phone for delivery)
        try {
            \App\Services\SmsMisrService::sendOtp($request->phone, $code);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send OTP SMS: ' . $e->getMessage());
            return $this->errorResponse('errors.sms_sending_failed', [
                'code' => $code,
            ], 500);
        }

        return $this->successResponse([
            'code' => $code,
        ], 'success.reset_code_sent');
    }

    public function reset(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'phone' => 'required|string',
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Resolve the phone: try as-is first, then without leading "2"
        $phone = $request->phone;
        $user = User::where('phone', $phone)->first();

        if (!$user && str_starts_with($phone, '2')) {
            $phoneWithout2 = substr($phone, 1);
            $user = User::where('phone', $phoneWithout2)->first();
            if ($user) {
                $phone = $phoneWithout2;
            }
        }

        if (!$user) {
            return $this->errorResponse('errors.phone_not_found', [], 404);
        }

        $record = DB::table('phone_verifications')
            ->where('phone', $phone)
            ->where('code', $request->code)
            ->first();

        if (!$record) {
            return $this->errorResponse('errors.invalid_reset_code', [], 400);
        }

        // Check expiration
        if (Carbon::now()->gt(Carbon::parse($record->expires_at))) {
            DB::table('phone_verifications')->where('phone', $phone)->delete();
            return $this->errorResponse('errors.reset_code_expired', [], 400);
        }

        // Update Password
        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        // Delete token
        DB::table('phone_verifications')->where('phone', $phone)->delete();

        // Log the password reset activity
        activity('user')
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip_address' => $request->ip(),
            ])
            ->log('User reset password via OTP');

        return $this->successResponse(null, 'success.password_reset_successful');
    }
}
