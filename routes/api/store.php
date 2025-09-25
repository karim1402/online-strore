<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StoreUser\AuthController;

/*
|--------------------------------------------------------------------------
| Store User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for store users.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('store')->group(function () {
    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:store_users');
        Route::post('refresh', 'refresh')->middleware('auth:store_users');
        Route::get('profile', 'profile')->middleware('auth:store_users');
    });

    // Protected store routes
    Route::middleware('auth:store_users')->group(function () {
        // Add more store-specific routes here
        Route::get('dashboard', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'Store Dashboard',
                'message_ar' => 'لوحة تحكم المتجر',
                'guard' => 'store_users'
            ]);
        });
        
        Route::get('products', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'Store Products',
                'message_ar' => 'منتجات المتجر',
                'guard' => 'store_users'
            ]);
        });
    });
});
