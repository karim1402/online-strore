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
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

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
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $credentials = $request->only('email', 'password');

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
}
