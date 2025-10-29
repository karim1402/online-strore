<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\AddressController;
use App\Http\Controllers\Api\User\StoreController;
use App\Http\Controllers\Api\User\MainCategoryController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\Api\User\CartController;

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
        // Dashboard
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

        // Address Management Routes
        Route::controller(AddressController::class)->prefix('addresses')->group(function () {
            Route::get('/', 'index')->name('user.addresses.index');
            Route::get('/default', 'getDefault')->name('user.addresses.default');
            Route::post('/', 'store')->name('user.addresses.store');
            Route::get('/{id}', 'show')->name('user.addresses.show');
            Route::put('/{id}', 'update')->name('user.addresses.update');
            Route::delete('/{id}', 'destroy')->name('user.addresses.destroy');
            Route::patch('/{id}/set-default', 'setDefault')->name('user.addresses.setDefault');
        });

        // Cart Management Routes
        Route::controller(CartController::class)->prefix('cart')->group(function () {
            Route::get('/', 'index')->name('user.cart.index');
            Route::post('/items', 'addItem')->name('user.cart.addItem');
            Route::put('/items/{itemId}', 'updateQuantity')->name('user.cart.updateQuantity');
            Route::delete('/items/{itemId}', 'removeItem')->name('user.cart.removeItem');
            Route::delete('/', 'clear')->name('user.cart.clear');
            Route::post('/replace', 'replace')->name('user.cart.replace');
        });
    });

    // Main Category routes (public)
    Route::controller(MainCategoryController::class)->prefix('main-categories')->group(function () {
        Route::get('/', 'index')->name('user.mainCategories.index');
        Route::get('/{id}', 'show')->name('user.mainCategories.show');
    });

    // Store routes (public, works for both guests and authenticated users)
    Route::controller(StoreController::class)->prefix('stores')->group(function () {
        Route::get('/by-category/{mainCategoryId}', 'getStoresByMainCategory')->name('user.stores.byCategory');
        Route::get('/{storeId}/categories', 'getStoreCategoriesWithProducts')->name('user.stores.categories');
    });

    // Product routes (public, works for both guests and authenticated users)
    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('/random', 'random')->name('user.products.random');
        Route::get('/{productId}', 'show')->name('user.products.show');
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
