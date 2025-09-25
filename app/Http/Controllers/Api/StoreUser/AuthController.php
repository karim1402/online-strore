<?php

namespace App\Http\Controllers\Api\StoreUser;

use App\Http\Controllers\Controller;
use App\Models\StoreUser;
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
     * Register a new store user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:store_users',
            'password' => 'required|string|confirmed|min:6',
            'store_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message_en' => $validator->errors()->first(),
                'message_ar' => 'خطأ في التحقق من البيانات'
            ], 422);
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

        return response()->json([
            'success' => true,
            'message_en' => 'Store user successfully registered',
            'message_ar' => 'تم تسجيل مستخدم المتجر بنجاح',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('store_users')->factory()->getTTL() * 60,
            'user' => $storeUser
        ], 201);
    }

    /**
     * Login store user
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

        if (!$token = Auth::guard('store_users')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message_en' => 'Invalid email or password',
                'message_ar' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }

        $storeUser = Auth::guard('store_users')->user();
        
        // Check if store user is active
        if (!$storeUser->status) {
            Auth::guard('store_users')->logout();
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
            'expires_in' => Auth::guard('store_users')->factory()->getTTL() * 60,
            'user' => $storeUser
        ]);
    }

    /**
     * Get store user profile
     */
    public function profile(): JsonResponse
    {
        $storeUser = Auth::guard('store_users')->user();

        return response()->json([
            'success' => true,
            'message_en' => 'Profile fetched successfully',
            'message_ar' => 'تم جلب الملف الشخصي بنجاح',
            'user' => $storeUser
        ]);
    }

    /**
     * Logout store user
     */
    public function logout(): JsonResponse
    {
        Auth::guard('store_users')->logout();

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
        $token = Auth::guard('store_users')->refresh();

        return response()->json([
            'success' => true,
            'message_en' => 'Token refreshed successfully',
            'message_ar' => 'تم تحديث الرمز بنجاح',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('store_users')->factory()->getTTL() * 60
        ]);
    }
}
