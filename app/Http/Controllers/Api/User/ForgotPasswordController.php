<?php

namespace App\Http\Controllers\Api\User;

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
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Delete existing tokens
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Generate OTP
        $code = rand(1000, 9999);

        // Store OTP
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $code, // Storing unhashed for simplicity as it's a short OTP. Secure approach would be hash but then verify is harder without bcrypt. Code is short lifetime.
            'created_at' => Carbon::now()
        ]);

        // Send Email
        try {
            Mail::to($request->email)->send(new PasswordResetMail($code));
        } catch (\Exception $e) {
            return $this->errorResponse('errors.email_sending_failed', [], 500);
        }

        return $this->successResponse(null, 'success.reset_code_sent');
    }

    public function reset(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->code)
            ->first();

        if (!$record) {
            return $this->errorResponse('errors.invalid_reset_code', [], 400);
        }

        // Check expiration (e.g., 15 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return $this->errorResponse('errors.reset_code_expired', [], 400);
        }

        // Update Password
        $user = User::where('email', $request->email)->first();
        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

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
