<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\AuthController;
use App\Http\Controllers\Api\Vendor\StoreController;
use App\Http\Controllers\Api\Vendor\CategoryController;

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
        Route::put('profile', 'updateProfile')->middleware('auth:vendors');
    });

    // Categories routes (public for registration)
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'index');
    });

    // Protected vendor routes
    Route::middleware('auth:vendors')->group(function () {
        // Store management routes
        Route::controller(StoreController::class)->prefix('store')->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            // Route::post('/', 'update'); // For form-data with _method=PUT
        });

        // Add more vendor-specific routes here
        Route::get('dashboard', function () {
            $vendor = auth('vendors')->user();
            $vendor->load(['store.mainCategories']);
            
            $message = \App\Services\LocalizationService::getMessage('guards.vendor_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'vendor' => $vendor
                ],
                'guard' => 'vendors'
            ]);
        });
        
        Route::get('products', function () {
            $vendor = auth('vendors')->user();
            $message = \App\Services\LocalizationService::getMessage('guards.vendor_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'info' => 'Vendor Products',
                    'store_id' => $vendor->store?->id
                ],
                'guard' => 'vendors'
            ]);
        });
    });
});
