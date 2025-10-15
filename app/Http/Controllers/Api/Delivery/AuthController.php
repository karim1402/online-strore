<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
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
     * Register a new delivery user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:deliveries',
            'password' => 'required|string|confirmed|min:6',
            'phone' => 'required|string|max:20',
            'vehicle_type' => 'required|string|in:bike,car,van,truck',
            'vehicle_number' => 'required|string|max:50',
            'license_number' => 'required|string|max:50',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $delivery = Delivery::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_number' => $request->vehicle_number,
            'license_number' => $request->license_number,
            'address' => $request->address,
            'status' => true,
            'availability' => true,
        ]);

        // Log the registration activity
        activity('delivery')
            ->causedBy($delivery)
            ->performedOn($delivery)
            ->withProperties([
                'vehicle_type' => $request->vehicle_type,
                'ip_address' => $request->ip(),
            ])
            ->log('Delivery user registered');

        $token = Auth::guard('deliveries')->login($delivery);

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('deliveries')->factory()->getTTL() * 60,
            'user' => $delivery
        ], 'success.delivery_registered', [], 201);
    }

    /**
     * Login delivery user
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

        if (!$token = Auth::guard('deliveries')->attempt($credentials)) {
            return $this->errorResponse('errors.invalid_credentials', [], 401);
        }

        $delivery = Auth::guard('deliveries')->user();
        
        // Check if delivery user is active
        if (!$delivery->status) {
            Auth::guard('deliveries')->logout();
            return $this->errorResponse('errors.account_disabled', [], 403);
        }

        // Log the login activity
        activity('delivery')
            ->causedBy($delivery)
            ->performedOn($delivery)
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Delivery user logged in');

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('deliveries')->factory()->getTTL() * 60,
            'user' => $delivery
        ], 'success.delivery_logged_in');
    }

    /**
     * Get delivery user profile
     */
    public function profile(): JsonResponse
    {
        $delivery = Auth::guard('deliveries')->user();

        return $this->successResponse($delivery, 'success.profile_fetched');
    }

    /**
     * Logout delivery user
     */
    public function logout(): JsonResponse
    {
        $delivery = Auth::guard('deliveries')->user();
        
        // Log the logout activity
        if ($delivery) {
            activity('delivery')
                ->causedBy($delivery)
                ->performedOn($delivery)
                ->log('Delivery user logged out');
        }
        
        Auth::guard('deliveries')->logout();

        return $this->successResponse(null, 'success.delivery_logged_out');
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = Auth::guard('deliveries')->refresh();
        } catch (\Exception $e) {
            return $this->errorResponse('errors.token_refresh_failed', [], 401);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('deliveries')->factory()->getTTL() * 60
        ], 'success.token_refreshed');
    }
}
