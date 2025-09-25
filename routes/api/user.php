<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for regular users.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('user')->group(function () {
    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register')->name('user.register');
        Route::post('login', 'login')->name('user.login');
        Route::post('logout', 'logout')->middleware('auth:api')->name('user.logout');
        Route::post('refresh', 'refresh')->middleware('auth:api')->name('user.refresh');
        Route::get('profile', 'profile')->middleware('auth:api')->name('user.profile');
    });

    // Protected user routes
    Route::middleware('auth:api')->group(function () {
        // Add more user-specific routes here
        Route::get('dashboard', function () {
            $user = auth('api')->user();
            $message = \App\Services\LocalizationService::getMessage('guards.user_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'guard' => 'api',
                    'user' => $user
                ]
            ]);
        })->name('user.dashboard');
    });
    
    // Test route without authentication
    Route::get('test', function () {
        $message = \App\Services\LocalizationService::getMessage('success.api_working');
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'guard' => 'api'
            ]
        ]);
    })->name('user.test');
});
