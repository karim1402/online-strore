<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\AuthController;

/*
|--------------------------------------------------------------------------
| Vendor API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for vendors.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('vendor')->group(function () {
    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:vendors');
        Route::post('refresh', 'refresh')->middleware('auth:vendors');
        Route::get('profile', 'profile')->middleware('auth:vendors');
    });

    // Protected vendor routes
    Route::middleware('auth:vendors')->group(function () {
        // Add more vendor-specific routes here
        Route::get('dashboard', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'Vendor Dashboard',
                'message_ar' => 'لوحة تحكم البائع',
                'guard' => 'vendors'
            ]);
        });
        
        Route::get('products', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'Vendor Products',
                'message_ar' => 'منتجات البائع',
                'guard' => 'vendors'
            ]);
        });
    });
});
