<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\User\AuthController;
use App\Http\Controllers\Api\V2\User\AddressController;
use App\Http\Controllers\Api\V2\User\StoreController;
use App\Http\Controllers\Api\V2\User\ModuleController;
use App\Http\Controllers\Api\V2\User\ModuleAdController;
use App\Http\Controllers\Api\V2\User\ProductController;
use App\Http\Controllers\Api\V2\User\CartController;
use App\Http\Controllers\Api\V2\User\OrderController;
use App\Http\Controllers\Api\V2\User\CategoryController;
use App\Http\Controllers\Api\V2\User\SocialAuthController;
use App\Http\Controllers\Api\V2\User\ForgotPasswordController;
use App\Http\Controllers\Api\V2\User\NotificationController;
use App\Http\Controllers\Api\V2\User\VoucherController;
use App\Http\Controllers\Api\V2\User\PaymentController;
use App\Http\Controllers\Api\V2\User\HomeAdController;
use App\Http\Controllers\Api\V2\User\SplashAdController;
use App\Http\Controllers\Api\V2\User\WorkingHoursController;
use App\Http\Controllers\Api\V2\User\SettingsController;
use App\Http\Controllers\Api\V2\User\ChatController;
use App\Http\Controllers\Api\V2\User\FeaturedSectionController;

/*
|--------------------------------------------------------------------------
| User API V2 Routes
|--------------------------------------------------------------------------
|
| All routes here are served under /api/v2/user/*.
| v1 routes (/api/user/*) are NOT changed.
| The auth:api guard (JWT) is shared between v1 and v2.
| Every controller here extends its v1 counterpart — override per method.
|
*/

Route::prefix('user')->group(function () {

    // ─── Authentication ───────────────────────────────────────────────
    Route::controller(AuthController::class)->group(function () {
        Route::post('send-otp',                 'sendOtp')->name('v2.user.send-otp');
        Route::post('register',                 'register')->name('v2.user.register');
        Route::post('login',                    'login')->name('v2.user.login');
        Route::post('logout',                   'logout')->middleware('auth:api')->name('v2.user.logout');
        Route::post('refresh',                  'refresh')->middleware('auth:api')->name('v2.user.refresh');
        Route::get('profile',                   'profile')->middleware('auth:api')->name('v2.user.profile');
        Route::post('update-profile',           'updateProfile')->middleware('auth:api')->name('v2.user.update-profile');
        Route::post('change-password',          'changePassword')->middleware('auth:api')->name('v2.user.change-password');
        Route::post('fcm-token',                'updateFcmToken')->name('v2.user.fcm-token');
        Route::delete('delete-account',         'deleteAccount')->middleware('auth:api')->name('v2.user.delete-account');
        Route::post('verify-email',             'verifyEmail')->name('v2.user.verify-email');
        Route::post('resend-verification-code', 'resendVerificationCode')->name('v2.user.resend-verification-code');
    });

    // ─── Social Authentication ────────────────────────────────────────
    Route::controller(SocialAuthController::class)->group(function () {
        Route::post('auth/google',   'loginWithGoogle')->name('v2.user.auth.google');
        Route::post('auth/facebook', 'loginWithFacebook')->name('v2.user.auth.facebook');
        Route::post('auth/apple',    'loginWithApple')->name('v2.user.auth.apple');
    });

    // ─── Forgot Password ──────────────────────────────────────────────
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::post('forgot-password', 'sendResetCode')->name('v2.user.forgot-password');
        Route::post('reset-password',  'reset')->name('v2.user.reset-password');
    });

    // ─── Protected Routes ─────────────────────────────────────────────
    Route::middleware('auth:api')->group(function () {

        // Dashboard
        Route::get('dashboard', function () {
            $user    = auth('api')->user();
            $message = \App\Services\LocalizationService::getMessage('guards.user_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => ['guard' => 'api', 'user' => $user, 'version' => 'v2'],
            ]);
        })->name('v2.user.dashboard');

        // Address Management
        Route::controller(AddressController::class)->prefix('addresses')->group(function () {
            Route::get('/',                   'index')->name('v2.user.addresses.index');
            Route::get('/default',            'getDefault')->name('v2.user.addresses.default');
            Route::post('/',                  'store')->name('v2.user.addresses.store');
            Route::get('/{id}',               'show')->name('v2.user.addresses.show');
            Route::put('/{id}',               'update')->name('v2.user.addresses.update');
            Route::delete('/{id}',            'destroy')->name('v2.user.addresses.destroy');
            Route::patch('/{id}/set-default', 'setDefault')->name('v2.user.addresses.setDefault');
            Route::get('/{id}/delivery-fees', 'deliveryFees')->name('v2.user.addresses.deliveryFees');
        });

        // Cart Management
        Route::controller(CartController::class)->prefix('cart')->group(function () {
            Route::get('/',                  'index')->name('v2.user.cart.index');
            Route::post('/items',            'addItem')->name('v2.user.cart.addItem');
            Route::put('/items/{itemId}',    'updateQuantity')->name('v2.user.cart.updateQuantity');
            Route::patch('/items/{itemId}',  'updateItem')->name('v2.user.cart.updateItem');
            Route::delete('/items/{itemId}', 'removeItem')->name('v2.user.cart.removeItem');
            Route::delete('/',               'clear')->name('v2.user.cart.clear');
            Route::post('/replace',          'replace')->name('v2.user.cart.replace');
        });

        // Order Management
        Route::post('checkout', [OrderController::class, 'checkout'])->name('v2.user.checkout');
        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::get('/',                           'index')->name('v2.user.orders.index');
            Route::get('/{orderId}',                  'show')->name('v2.user.orders.show');
            Route::post('/{orderId}/cancel',          'cancel')->name('v2.user.orders.cancel');
            Route::post('/{orderId}/confirm-payment', 'confirmPayment')->name('v2.user.orders.confirmPayment');
            Route::post('/{orderId}/chat',            [ChatController::class, 'sendMessage'])->name('v2.user.orders.chat');
        });

        // Notifications
        Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
            Route::get('/',               'index')->name('v2.user.notifications.index');
            Route::get('/unread-count',   'getUnreadCount')->name('v2.user.notifications.unreadCount');
            Route::post('/mark-all-read', 'markAllAsRead')->name('v2.user.notifications.markAllRead');
            Route::post('/{id}/read',     'markAsRead')->name('v2.user.notifications.markRead');
        });

        // Vouchers
        Route::post('vouchers/verify', [VoucherController::class, 'verify'])->name('v2.user.vouchers.verify');

        // Payments (protected)
        Route::post('payments/create-intention', [PaymentController::class, 'createIntention'])->name('v2.user.payments.create-intention');
    });

    // ─── Public Routes ────────────────────────────────────────────────

    // Modules
    Route::controller(ModuleController::class)->prefix('modules')->group(function () {
        Route::get('/',      'index')->name('v2.user.modules.index');
        Route::get('/{id}',  'show')->name('v2.user.modules.show');
        Route::get('/{id}/ads', [ModuleAdController::class, 'getByModule'])->name('v2.user.modules.ads');
    });

    // Categories
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/{id}/all-with-products', 'getAllWithProducts')->name('v2.user.categories.allWithProducts');
        Route::get('/by-module/{moduleId}',   'getByModule')->name('v2.user.categories.byModule');
        Route::get('/{id}',                   'show')->name('v2.user.categories.show');
        Route::get('/{id}/subcategories',     'getSubcategories')->name('v2.user.categories.subcategories');
    });

    // Stores
    Route::controller(StoreController::class)->prefix('stores')->group(function () {
        Route::get('/by-module/{moduleId}', 'getStoresByModule')->name('v2.user.stores.byModule');
        Route::get('/{storeId}/categories', 'getStoreCategoriesWithProducts')->name('v2.user.stores.categories');
    });

    // Products
    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('/best-sellers',            'bestSellers')->name('v2.user.products.bestSellers');
        Route::get('/search',                  'search')->name('v2.user.products.search');
        Route::get('/random',                  'random')->name('v2.user.products.random');
        Route::get('/product-makook-sandwich', 'showMakookSandwich')->name('v2.user.products.showMakookSandwich');
        Route::get('/category/{categoryId}',   'byCategory')->name('v2.user.products.byCategory');
        Route::get('/{productId}',             'show')->name('v2.user.products.show');
    });

    // Home Ads
    Route::get('home-ads',               [HomeAdController::class, 'index'])->name('v2.user.home-ads.index');
    Route::get('home-ads/{id}/products', [HomeAdController::class, 'showProducts'])->name('v2.user.home-ads.products');

    // Featured Sections
    Route::get('featured-sections', [FeaturedSectionController::class, 'index'])->name('v2.user.featured-sections.index');

    // Service Area check
    Route::post('check-service-area', [AddressController::class, 'checkServiceArea'])->name('v2.user.check-service-area');

    // Splash Ad
    Route::get('splash-ad', [SplashAdController::class, 'getRandom'])->name('v2.user.splash-ad.random');

    // Payment webhook (public)
    Route::post('payments/webhook', [PaymentController::class, 'webhook'])->name('v2.user.payments.webhook');

    // Working Hours
    Route::get('working-hours', [WorkingHoursController::class, 'index'])->name('v2.user.working-hours');

    // App Settings
    Route::get('settings',    [SettingsController::class, 'index'])->name('v2.user.settings');
    Route::get('app-version', [SettingsController::class, 'appVersion'])->name('v2.user.app-version');

    // Test route
    Route::get('test', function () {
        $message = \App\Services\LocalizationService::getMessage('success.api_working');
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => ['guard' => 'api', 'version' => 'v2'],
        ]);
    })->name('v2.user.test');
});
