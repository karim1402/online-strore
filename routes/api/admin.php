<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for admin users.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('admin')->group(function () {
    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:admins');
        Route::post('refresh', 'refresh')->middleware('auth:admins');
        Route::get('profile', 'profile')->middleware('auth:admins');
    });

    // Protected admin routes
    Route::middleware('auth:admins')->group(function () {
        // Add more admin-specific routes here
        Route::get('dashboard', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'Admin Dashboard',
                'message_ar' => 'لوحة تحكم المدير',
                'guard' => 'admins'
            ]);
        });
        
        Route::get('users', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'All Users Management',
                'message_ar' => 'إدارة جميع المستخدمين',
                'guard' => 'admins'
            ]);
        });
        
        Route::get('settings', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'System Settings',
                'message_ar' => 'إعدادات النظام',
                'guard' => 'admins'
            ]);
        });
    });
});
