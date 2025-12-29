<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Vendor\AuthController;
use App\Http\Controllers\Api\Vendor\StoreController;
use App\Http\Controllers\Api\Vendor\CategoryController;
use App\Http\Controllers\Api\Vendor\BranchController;
use App\Http\Controllers\Api\Vendor\ProductController;
use App\Http\Controllers\Api\Vendor\OptionGroupController;
use App\Http\Controllers\Api\Vendor\OptionValueController;
use App\Http\Controllers\Api\Vendor\AddonController;
use App\Http\Controllers\Api\Vendor\ProductOptionController;
use App\Http\Controllers\Api\Vendor\ProductAddonController;
use App\Http\Controllers\Api\Vendor\MainCategoryController;
use App\Http\Controllers\Api\Vendor\OrderController;

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
    // Public routes (no authentication required)
    Route::controller(MainCategoryController::class)->prefix('main-categories')->group(function () {
        Route::get('/', 'getPublicMainCategories');
    });

    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:vendors');
        Route::post('refresh', 'refresh')->middleware('auth:vendors');
        Route::get('profile', 'profile')->middleware('auth:vendors');
        Route::put('profile', 'updateProfile')->middleware('auth:vendors');
        Route::post('profile', 'updateProfile')->middleware('auth:vendors'); // POST alternative for file uploads
        Route::post('fcm-token', 'updateFcmToken')->middleware('auth:vendors');
    });

    // Protected vendor routes
    Route::middleware('auth:vendors')->group(function () {
        // Store management routes
        Route::controller(StoreController::class)->prefix('store')->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            Route::post('/', 'update'); // POST alternative for file uploads
        });

        // Branch management routes
        Route::controller(BranchController::class)->prefix('branches')->group(function () {
            Route::get('/', 'index');
            Route::get('/main', 'getMainBranch');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/toggle-status', 'toggleStatus');
            Route::patch('/{id}/set-main', 'setAsMain');
        });

        // Category management routes
        Route::controller(CategoryController::class)->prefix('categories')->group(function () {
            Route::get('/', 'index');
            
            // Static routes MUST come before dynamic routes
            Route::post('/update-sort-order', 'updateSortOrder');
            
            // Dynamic routes with parameters
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::post('/{id}', 'update'); // POST alternative for file uploads
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/toggle-status', 'toggleStatus');
        });

        // Product Management routes
        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            
            // Static routes MUST come before dynamic routes
            Route::post('/reorder', 'reorderProducts');
            Route::post('/images/reorder', 'reorderImages');
            
            // Dynamic routes with parameters
            Route::put('/{id}', 'update');
            Route::post('/{id}', 'update'); // POST alternative for file uploads
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/toggle-status', 'toggleStatus');
            Route::post('/{id}/duplicate', 'duplicate');
            Route::post('/{id}/images', 'uploadImages');
            Route::delete('/images/{id}', 'deleteImage');
            Route::patch('/images/{id}/set-primary', 'setPrimaryImage');
        });

        // Option Group Management routes
        Route::controller(OptionGroupController::class)->prefix('option-groups')->group(function () {
            Route::get('/', 'index');
            Route::get('/types', 'getTypes');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::patch('/{id}/toggle-status', 'toggleStatus');
            Route::delete('/{id}', 'destroy');
        });

        // Option Value Management routes
        Route::controller(OptionValueController::class)->prefix('option-values')->group(function () {
            Route::get('/group/{groupId}', 'index');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::post('/reorder', 'reorder');
            Route::delete('/{id}', 'destroy');
        });

        // Addon Management routes
        Route::controller(AddonController::class)->prefix('addons')->group(function () {
            Route::get('/', 'index');
            Route::get('/categories', 'getCategories');
            Route::get('/{id}', 'show');
            Route::post('/', 'store');
            Route::put('/{id}', 'update');
            Route::patch('/{id}/toggle-status', 'toggleStatus');
            Route::delete('/{id}', 'destroy');
        });

        // Product Option Management routes (assign options to products)
        Route::controller(ProductOptionController::class)->prefix('product-options')->group(function () {
            Route::get('/product/{productId}', 'index');
            Route::post('/assign-group', 'assignOptionGroup');
            Route::put('/{id}', 'updateOptionGroup');
            Route::delete('/{id}', 'removeOptionGroup');
            Route::post('/assign-values', 'assignOptionValues');
            Route::put('/values/{id}', 'updateOptionValue');
            Route::patch('/values/{id}/stock', 'updateStock');
            Route::delete('/values/{id}', 'removeOptionValue');
        });

        // Product Addon Management routes (assign addons to products)
        Route::controller(ProductAddonController::class)->prefix('product-addons')->group(function () {
            Route::get('/product/{productId}', 'index');
            Route::post('/assign', 'assignAddons');
            Route::put('/product/{productId}/addon/{addonId}', 'updateAddon');
            Route::delete('/product/{productId}/addon/{addonId}', 'removeAddon');
            Route::post('/reorder', 'reorderAddons');
        });

        Route::controller(OrderController::class)->prefix('orders')->group(function () {
            Route::get('/', 'index');
            Route::patch('/{id}/ready-to-pick', 'markReadyToPick');
            Route::patch('/{id}/cancel', 'cancel');
            Route::get('/{id}', 'show');
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
        
        // Route::get('products', function () {
        //     $vendor = auth('vendors')->user();
        //     $message = \App\Services\LocalizationService::getMessage('guards.vendor_dashboard');
        //     return response()->json([
        //         'success' => true,
        //         'message' => $message,
        //         'data' => [
        //             'info' => 'Vendor Products',
        //             'store_id' => $vendor->store?->id
        //         ],
        //         'guard' => 'vendors'
        //     ]);
        // });
    });
});
