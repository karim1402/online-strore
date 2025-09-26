<?php

namespace App\Http\Controllers\Api\StoreUser;

use App\Http\Controllers\Controller;
use App\Models\StoreUser;
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
     * Register a new store user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:store_users',
            'password' => 'required|string|confirmed|min:6',
            'store_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $storeUser = StoreUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'store_name' => $request->store_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => true,
        ]);

        $token = Auth::guard('store_users')->login($storeUser);

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('store_users')->factory()->getTTL() * 60,
            'user' => $storeUser
        ], 'success.store_user_registered', [], 201);
    }

    /**
     * Login store user
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

        if (!$token = Auth::guard('store_users')->attempt($credentials)) {
            return $this->errorResponse('errors.invalid_credentials', [], 401);
        }

        $storeUser = Auth::guard('store_users')->user();
        
        // Check if store user is active
        if (!$storeUser->status) {
            Auth::guard('store_users')->logout();
            return $this->errorResponse('errors.account_disabled', [], 403);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('store_users')->factory()->getTTL() * 60,
            'user' => $storeUser
        ], 'success.store_user_logged_in');
    }

    /**
     * Get store user profile
     */
    public function profile(): JsonResponse
    {
        $storeUser = Auth::guard('store_users')->user();

        return $this->successResponse($storeUser, 'success.profile_fetched');
    }

    /**
     * Logout store user
     */
    public function logout(): JsonResponse
    {
        Auth::guard('store_users')->logout();

        return $this->successResponse(null, 'success.store_user_logged_out');
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = Auth::guard('store_users')->refresh();
        } catch (\Exception $e) {
            return $this->errorResponse('errors.token_refresh_failed', [], 401);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('store_users')->factory()->getTTL() * 60
        ], 'success.token_refreshed');
    }
}
