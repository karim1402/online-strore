<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
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
     * Register a new vendor
     */
    public function register(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:vendors',
            'password' => 'required|string|confirmed|min:6',
            'store_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $vendor = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'store_name' => $request->store_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'status' => true,
        ]);

        $token = Auth::guard('vendors')->login($vendor);

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('vendors')->factory()->getTTL() * 60,
            'user' => $vendor
        ], 'success.vendor_registered', [], 201);
    }

    /**
     * Login vendor
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

        if (!$token = Auth::guard('vendors')->attempt($credentials)) {
            return $this->errorResponse('errors.invalid_credentials', [], 401);
        }

        $vendor = Auth::guard('vendors')->user();
        
        // Check if vendor is active
        if (!$vendor->status) {
            Auth::guard('vendors')->logout();
            return $this->errorResponse('errors.account_disabled', [], 403);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('vendors')->factory()->getTTL() * 60,
            'user' => $vendor
        ], 'success.vendor_logged_in');
    }

    /**
     * Get vendor profile
     */
    public function profile(): JsonResponse
    {
        $vendor = Auth::guard('vendors')->user();

        return $this->successResponse($vendor, 'success.profile_fetched');
    }

    /**
     * Logout vendor
     */
    public function logout(): JsonResponse
    {
        Auth::guard('vendors')->logout();

        return $this->successResponse(null, 'success.vendor_logged_out');
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = Auth::guard('vendors')->refresh();
        } catch (\Exception $e) {
            return $this->errorResponse('errors.token_refresh_failed', [], 401);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('vendors')->factory()->getTTL() * 60
        ], 'success.token_refreshed');
    }
}
