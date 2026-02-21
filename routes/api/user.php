<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\AddressController;
use App\Http\Controllers\Api\User\StoreController;
use App\Http\Controllers\Api\User\ModuleController;
use App\Http\Controllers\Api\User\ProductController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\User\OrderController;

use App\Http\Controllers\Api\User\CategoryController;

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
        Route::post('send-otp', 'sendOtp')->name('user.send-otp');
        Route::post('register', 'register')->name('user.register');
        Route::post('login', 'login')->name('user.login');
        Route::post('logout', 'logout')->middleware('auth:api')->name('user.logout');
        Route::post('refresh', 'refresh')->middleware('auth:api')->name('user.refresh');
        Route::get('profile', 'profile')->middleware('auth:api')->name('user.profile');
        Route::post('update-profile', 'updateProfile')->middleware('auth:api')->name('user.update-profile');
        Route::post('change-password', 'changePassword')->middleware('auth:api')->name('user.change-password');
        Route::post('fcm-token', 'updateFcmToken')->name('user.fcm-token');
        Route::delete('delete-account', 'deleteAccount')->middleware('auth:api')->name('user.delete-account');
        Route::post('verify-email', 'verifyEmail')->name('user.verify-email');
        Route::post('resend-verification-code', 'resendVerificationCode')->name('user.resend-verification-code');
    });

    // Social Authentication routes
    Route::controller(\App\Http\Controllers\Api\User\SocialAuthController::class)->group(function () {
        Route::post('auth/google', 'loginWithGoogle')->name('user.auth.google');
        Route::post('auth/facebook', 'loginWithFacebook')->name('user.auth.facebook');
        Route::post('auth/apple', 'loginWithApple')->name('user.auth.apple');
    });

    // Forgot Password routes
    Route::controller(\App\Http\Controllers\Api\User\ForgotPasswordController::class)->group(function () {
        Route::post('forgot-password', 'sendResetCode')->name('user.forgot-password');
        Route::post('reset-password', 'reset')->name('user.reset-password');
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
            Route::patch('/items/{itemId}', 'updateItem')->name('user.cart.updateItem');
            Route::delete('/items/{itemId}', 'removeItem')->name('user.cart.removeItem');
            Route::delete('/', 'clear')->name('user.cart.clear');
            Route::post('/replace', 'replace')->name('user.cart.replace');
        });

        // Order Management Routes
        Route::post('checkout', [OrderController::class, 'checkout'])->name('user.checkout');
        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::get('/', 'index')->name('user.orders.index');
            Route::get('/{orderId}', 'show')->name('user.orders.show');
            Route::post('/{orderId}/cancel', 'cancel')->name('user.orders.cancel');
            Route::post('/{orderId}/confirm-payment', 'confirmPayment')->name('user.orders.confirmPayment');
            Route::post('/{orderId}/chat', [\App\Http\Controllers\Api\User\ChatController::class, 'sendMessage'])->name('user.orders.chat');
        });

        // Voucher Routes
        Route::post('vouchers/verify', [\App\Http\Controllers\Api\User\VoucherController::class, 'verify'])->name('user.vouchers.verify');
    });

    // Module routes (public)
    Route::controller(ModuleController::class)->prefix('modules')->group(function () {
        Route::get('/', 'index')->name('user.modules.index');
        Route::get('/{id}', 'show')->name('user.modules.show');
        Route::get('/{id}/ads', [\App\Http\Controllers\Api\User\ModuleAdController::class, 'getByModule'])->name('user.modules.ads');
    });

    // Category routes (public)
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/{id}/all-with-products', 'getAllWithProducts')->name('user.categories.allWithProducts');
        Route::get('/by-module/{moduleId}', 'getByModule')->name('user.categories.byModule');
        Route::get('/{id}', 'show')->name('user.categories.show');
        Route::get('/{id}/subcategories', 'getSubcategories')->name('user.categories.subcategories');
    });

    // Store routes (public, works for both guests and authenticated users)
    Route::controller(StoreController::class)->prefix('stores')->group(function () {
        Route::get('/by-module/{moduleId}', 'getStoresByModule')->name('user.stores.byModule');
        Route::get('/{storeId}/categories', 'getStoreCategoriesWithProducts')->name('user.stores.categories');
    });

    // Product routes (public, works for both guests and authenticated users)
    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('/best-sellers', 'bestSellers')->name('user.products.bestSellers');
        Route::get('/search', 'search')->name('user.products.search');
        Route::get('/random', 'random')->name('user.products.random');
        Route::get('/product-makook-sandwich', 'showMakookSandwich')->name('user.products.showMakookSandwich');
        Route::get('/{productId}', 'show')->name('user.products.show');
    });
    
    // Home Ads route (public)
    Route::get('home-ads', [\App\Http\Controllers\Api\User\HomeAdController::class, 'index'])->name('user.home-ads.index');
    Route::get('home-ads/{id}/products', [\App\Http\Controllers\Api\User\HomeAdController::class, 'showProducts'])->name('user.home-ads.products');
    
    // Check Service Area (public)
    Route::post('check-service-area', [AddressController::class, 'checkServiceArea'])->name('user.check-service-area');

    // Payment Routes (public/protected)
    Route::post('payments/webhook', [\App\Http\Controllers\Api\User\PaymentController::class, 'webhook'])->name('user.payments.webhook');
    Route::middleware('auth:api')->post('payments/create-intention', [\App\Http\Controllers\Api\User\PaymentController::class, 'createIntention'])->name('user.payments.create-intention');

    // Working Hours check (public)
    Route::get('working-hours', [\App\Http\Controllers\Api\User\WorkingHoursController::class, 'index'])->name('user.working-hours');

    // App Settings (public)
    Route::get('settings', [\App\Http\Controllers\Api\User\SettingsController::class, 'index'])->name('user.settings');
    
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
