<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountVerificationMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        // Middleware will be handled in routes
    }

    /**
     * Send OTP to email for registration verification
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'email' => 'required|string|email|max:100|unique:users,email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Generate 4-digit OTP
        $code = rand(1000, 9999);

        // Store or update in email_verifications table
        \Illuminate\Support\Facades\DB::table('email_verifications')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(15),
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]
        );

        // Send OTP via email
        try {
            Mail::to($request->email)->send(new AccountVerificationMail($code));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send OTP email: ' . $e->getMessage());
            return $this->errorResponse('errors.email_sending_failed', [], 500);
        }

        return $this->successResponse(['code' => $code], 'success.otp_sent');
    }

    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'nullable|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|min:10|unique:users,phone',
            // 'otp' => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Verify OTP from email_verifications table
        // $verification = \Illuminate\Support\Facades\DB::table('email_verifications')
        //     ->where('email', $request->email)
        //     ->first();

        // if (!$verification) {
        //     return $this->errorResponse('errors.otp_not_found', [], 400);
        // }

        // if ($verification->code !== $request->otp) {
        //     return $this->errorResponse('errors.invalid_otp', [], 400);
        // }

        // if (Carbon::now()->gt(Carbon::parse($verification->expires_at))) {
        //     return $this->errorResponse('errors.otp_expired', [], 400);
        // }

        // Create user with verified email
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email??null,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'email_verified_at' => Carbon::now(),
        ]);

        // Delete the verification record
        // \Illuminate\Support\Facades\DB::table('email_verifications')
        //     ->where('email', $request->email)
        //     ->delete();

        // Log the registration activity
        activity('user')
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip_address' => $request->ip(),
            ])
            ->log('User registered');

        $token = Auth::guard('api')->login($user);

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 3600, // 1 hour
            'user' => $user
        ], 'success.user_registered', [], 201);
    }

    /**
     * Login user (can use email OR phone in the 'email' field)
     */
    public function login(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Detect if input is email or phone and set credentials accordingly
        $loginField = $request->email;
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            // Input is an email
            $credentials = ['email' => $loginField, 'password' => $request->password];
        } else {
            // Input is a phone number
            $credentials = ['phone' => $loginField, 'password' => $request->password];
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->errorResponse('errors.invalid_credentials', [], 401);
        }

        $user = Auth::guard('api')->user();

        // Log the login activity
        activity('user')
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('User logged in');

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 3600, // 1 hour
            'user' => $user
        ], 'success.user_logged_in');
    }

    /**
     * Get user profile
     */
    public function profile(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        return $this->successResponse($user, 'success.profile_fetched');
    }

    /**
     * Logout user
     */
    public function logout(): JsonResponse
    {
        $user = Auth::guard('api')->user();
        
        // Log the logout activity
        if ($user) {
            activity('user')
                ->causedBy($user)
                ->performedOn($user)
                ->log('User logged out');
        }
        
        Auth::guard('api')->logout();

        return $this->successResponse(null, 'success.user_logged_out');
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = auth('api')->refresh();
        } catch (\Exception $e) {
            return $this->errorResponse('errors.token_refresh_failed', [], 401);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 3600 // 1 hour
        ], 'success.token_refreshed');
    }

    /**
     * Update FCM token for push notifications
     */
    public function updateFcmToken(Request $request): JsonResponse
    {

        Log::info( $request->all());
        $validator = ValidationService::make($request->all(), [
            'device_id' => 'required|string|max:255',
            'fcm_token' => 'required|string',
            'platform' => 'nullable|string|in:ios,android,web',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $user = Auth::guard('api')->user();

        \App\Models\FcmToken::updateOrCreate(
            ['device_id' => $request->device_id],
            [
                'token' => $request->fcm_token,
                'tokenable_id' => $user ? $user->id : null,
                'tokenable_type' => $user ? get_class($user) : null,
                'platform' => $request->platform,
            ]
        );

        return $this->successResponse(null, 'success.fcm_token_updated');
    }

    /**
     * Verify user email with OTP
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return $this->errorResponse('errors.email_already_verified', [], 400);
        }

        if ($user->verification_code !== $request->code) {
             return $this->errorResponse('errors.invalid_verification_code', [], 400);
        }

        if (Carbon::now()->gt($user->verification_code_expires_at)) {
             return $this->errorResponse('errors.verification_code_expired', [], 400);
        }

        $user->email_verified_at = Carbon::now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        // Log the verification
        activity('user')
            ->causedBy($user)
            ->performedOn($user)
            ->log('User verified email');

        $token = Auth::guard('api')->login($user);

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 3600, // 1 hour
            'user' => $user
        ], 'success.email_verified');
    }

    /**
     * Resend verification code
     */
    public function resendVerificationCode(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $user = User::where('email', $request->email)->first();
        
        // Generate Verification Code
        $code = rand(1000, 9999);

        if ($user) {
            if ($user->email_verified_at) {
                return $this->errorResponse('errors.email_already_verified', [], 400);
            }

            $user->verification_code = $code;
            $user->verification_code_expires_at = Carbon::now()->addMinutes(15);
            $user->save();
        } else {
            // User does not exist, treat as registration resend (email_verifications)
            \Illuminate\Support\Facades\DB::table('email_verifications')->updateOrInsert(
                ['email' => $request->email],
                [
                    'code' => $code,
                    'expires_at' => Carbon::now()->addMinutes(15),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }

        // Send Verification Email
        try {
            Mail::to($request->email)->send(new AccountVerificationMail($code));
        } catch (\Exception $e) {
            return $this->errorResponse('errors.email_sending_failed', [], 500);
        }

        return $this->successResponse([
            "code" => $code
        ], 'success.verification_code_resent');
    }

    /**
     * Delete user account
     */
    /**
     * Delete user account
     */
    public function deleteAccount(): JsonResponse
    {
        $user = Auth::guard('api')->user();

        // Log the deletion activity
        if ($user) {
            activity('user')
                ->causedBy($user)
                ->performedOn($user)
                ->log('User deleted account');
            
            // Soft delete the user
            $user->delete();
        }

        Auth::guard('api')->logout();

        return $this->successResponse(null, 'success.account_deleted');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::guard('api')->user();

        $validator = ValidationService::make($request->all(), [
            'name' => 'required|string|between:2,100',
            // 'email' => 'required|string|email|max:100|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $user->update([
            'name' => $request->name,
            // 'email' => $request->email,
        ]);

        // Log the profile update activity
        activity('user')
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip_address' => $request->ip(),
            ])
            ->log('User updated profile');

        return $this->successResponse($user, 'success.profile_updated');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $user = Auth::guard('api')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse('errors.current_password_incorrect', [], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Log the password change activity
        activity('user')
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip_address' => $request->ip(),
            ])
            ->log('User changed password');

        return $this->successResponse(null, 'success.password_changed');
    }
}
