<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
     * Login admin
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

        if (!$token = Auth::guard('admins')->attempt($credentials)) {
            return $this->errorResponse('errors.invalid_credentials', [], 401);
        }

        $admin = Auth::guard('admins')->user();
        
        // Check if admin is active
        if (!$admin->status) {
            Auth::guard('admins')->logout();
            return $this->errorResponse('errors.account_disabled', [], 403);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('admins')->factory()->getTTL() * 60,
            'user' => $admin
        ], 'success.admin_logged_in');
    }

    /**
     * Get admin profile
     */
    public function profile(): JsonResponse
    {
        $admin = Auth::guard('admins')->user();

        return $this->successResponse($admin, 'success.profile_fetched');
    }

    /**
     * Update admin profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $admin = Auth::guard('admins')->user();

            $validator = ValidationService::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:admins,email,' . $admin->id,
                'password' => 'nullable|string|min:6',
                'phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();

            // Handle password update
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $admin->update($data);

            return $this->successResponse($admin->fresh(), 'success.profile_updated');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Logout admin
     */
    public function logout(): JsonResponse
    {
        Auth::guard('admins')->logout();

        return $this->successResponse(null, 'success.admin_logged_out');
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = Auth::guard('admins')->refresh();
        } catch (\Exception $e) {
            return $this->errorResponse('errors.token_refresh_failed', [], 401);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('admins')->factory()->getTTL() * 60
        ], 'success.token_refreshed');
    }
}
