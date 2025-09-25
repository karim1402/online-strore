<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
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
     * Register a new admin
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:admins',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'nullable|string|in:admin,super_admin,manager',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message_en' => $validator->errors()->first(),
                'message_ar' => 'خطأ في التحقق من البيانات'
            ], 422);
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'admin',
            'phone' => $request->phone,
            'status' => true,
        ]);

        $token = Auth::guard('admins')->login($admin);

        return response()->json([
            'success' => true,
            'message_en' => 'Admin successfully registered',
            'message_ar' => 'تم تسجيل المدير بنجاح',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('admins')->factory()->getTTL() * 60,
            'user' => $admin
        ], 201);
    }

    /**
     * Login admin
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

        if (!$token = Auth::guard('admins')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message_en' => 'Invalid email or password',
                'message_ar' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ], 401);
        }

        $admin = Auth::guard('admins')->user();
        
        // Check if admin is active
        if (!$admin->status) {
            Auth::guard('admins')->logout();
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
            'expires_in' => Auth::guard('admins')->factory()->getTTL() * 60,
            'user' => $admin
        ]);
    }

    /**
     * Get admin profile
     */
    public function profile(): JsonResponse
    {
        $admin = Auth::guard('admins')->user();

        return response()->json([
            'success' => true,
            'message_en' => 'Profile fetched successfully',
            'message_ar' => 'تم جلب الملف الشخصي بنجاح',
            'user' => $admin
        ]);
    }

    /**
     * Logout admin
     */
    public function logout(): JsonResponse
    {
        Auth::guard('admins')->logout();

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
        $token = Auth::guard('admins')->refresh();

        return response()->json([
            'success' => true,
            'message_en' => 'Token refreshed successfully',
            'message_ar' => 'تم تحديث الرمز بنجاح',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('admins')->factory()->getTTL() * 60
        ]);
    }
}
