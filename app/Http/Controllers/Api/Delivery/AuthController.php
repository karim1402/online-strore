<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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
        $validator = Validator::make($request->all(), [
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
            return response()->json([
                'success' => false,
                'message_en' => $validator->errors()->first(),
                'message_ar' => 'خطأ في التحقق من البيانات'
            ], 422);
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

        $token = Auth::guard('deliveries')->login($delivery);

        return response()->json([
            'success' => true,
            'message_en' => 'Delivery user successfully registered',
            'message_ar' => 'تم تسجيل عامل التوصيل بنجاح',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('deliveries')->factory()->getTTL() * 60,
            'user' => $delivery
        ], 201);
    }

    /**
     * Login delivery user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message_en' => $validator->errors()->first(),
                'message_ar' => 'خطأ في التحقق من البيانات'
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('deliveries')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message_en' => 'Invalid email or password',
                'message_ar' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }

        $delivery = Auth::guard('deliveries')->user();
        
        // Check if delivery user is active
        if (!$delivery->status) {
            Auth::guard('deliveries')->logout();
            return response()->json([
                'success' => false,
                'message_en' => 'Account is disabled',
                'message_ar' => 'الحساب معطل'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message_en' => 'Login successful',
            'message_ar' => 'تم تسجيل الدخول بنجاح',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('deliveries')->factory()->getTTL() * 60,
            'user' => $delivery
        ]);
    }

    /**
     * Get delivery user profile
     */
    public function profile(): JsonResponse
    {
        $delivery = Auth::guard('deliveries')->user();

        return response()->json([
            'success' => true,
            'message_en' => 'Profile fetched successfully',
            'message_ar' => 'تم جلب الملف الشخصي بنجاح',
            'user' => $delivery
        ]);
    }

    /**
     * Logout delivery user
     */
    public function logout(): JsonResponse
    {
        Auth::guard('deliveries')->logout();

        return response()->json([
            'success' => true,
            'message_en' => 'Successfully logged out',
            'message_ar' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        $token = Auth::guard('deliveries')->refresh();

        return response()->json([
            'success' => true,
            'message_en' => 'Token refreshed successfully',
            'message_ar' => 'تم تحديث الرمز بنجاح',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('deliveries')->factory()->getTTL() * 60
        ]);
    }
}
