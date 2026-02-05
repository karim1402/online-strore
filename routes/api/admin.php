<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\ModuleController;
use App\Http\Controllers\Api\Admin\StoreController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Controllers\Api\Admin\RoleAssignmentController;
use App\Http\Controllers\Api\Admin\BranchController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\OptionGroupController;
use App\Http\Controllers\Api\Admin\OptionValueController;
use App\Http\Controllers\Api\Admin\ProductOptionController;
use App\Http\Controllers\Api\Admin\AddonController;
use App\Http\Controllers\Api\Admin\ProductAddonController;
use App\Http\Controllers\Api\Admin\ActivityLogController;
use App\Http\Controllers\Api\Admin\DeliveryUserController; 
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\VendorInvoiceController;

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
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:admins');
        Route::post('refresh', 'refresh')->middleware('auth:admins');
        Route::get('profile', 'profile')->middleware('auth:admins');
        Route::put('profile', 'updateProfile')->middleware('auth:admins');
        Route::post('fcm-token', 'updateFcmToken')->middleware('auth:admins');
    });

    // Protected admin routes
    Route::middleware('auth:admins')->group(function () {
        // Add more admin-specific routes here
        Route::get('dashboard', function () {
            $message = \App\Services\LocalizationService::getMessage('guards.admin_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => null,
                'guard' => 'admins'
            ]);
        });
        
        // Regular Users CRUD routes (permission-based)
        Route::controller(UserController::class)->prefix('users')->group(function () {
            Route::middleware('permission:users.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:users.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:users.update,admins')->group(function () {
                Route::put('/{id}', 'update');
            });
            
            Route::middleware('permission:users.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });
        
        Route::get('settings', function () {
            $message = \App\Services\LocalizationService::getMessage('guards.admin_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => ['info' => 'System Settings'],
                'guard' => 'admins'
            ]);
        });

        // Modules CRUD routes (permission-based)
        Route::controller(ModuleController::class)->prefix('modules')->group(function () {
            Route::middleware('permission:categories.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/active', 'getActiveModules');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:categories.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:categories.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::post('/{id}', 'update'); // POST alternative for file uploads (verified)
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:categories.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Module Ads CRUD routes (permission-based)
        Route::controller(\App\Http\Controllers\Api\Admin\ModuleAdController::class)->prefix('module-ads')->group(function () {
            Route::middleware('permission:categories.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:categories.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:categories.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::post('/{id}', 'update'); // POST alternative for file uploads
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:categories.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Home Ads CRUD routes
        Route::controller(\App\Http\Controllers\Api\Admin\HomeAdController::class)->prefix('home-ads')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
            Route::patch('/{id}/toggle-status', 'toggleStatus');
        });

        // Admin Users CRUD routes (permission-based)
        Route::controller(AdminUserController::class)->prefix('admin-users')->group(function () {
            Route::middleware('permission:admin-users.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:admin-users.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:admin-users.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:admin-users.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Delivery Users CRUD routes (permission-based)
        Route::controller(DeliveryUserController::class)->prefix('delivery-users')->group(function () {
            Route::middleware('permission:delivery-users.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:delivery-users.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:delivery-users.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:delivery-users.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Store Management routes (permission-based)
        Route::controller(StoreController::class)->prefix('stores')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/all', 'getAll');
                Route::get('/pending', 'getPendingStores');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::post('/{id}', 'update'); // POST alternative for file uploads
            });
            
            Route::middleware('permission:stores.approve,admins')->group(function () {
                Route::post('/{id}/approve', 'approve');
                Route::post('/{id}/reject', 'reject');
                Route::post('/{id}/suspend', 'suspend');
                Route::post('/{id}/reactivate', 'reactivate');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Branch Management routes (permission-based)
        Route::controller(BranchController::class)->prefix('branches')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/store/{storeId}', 'getStoreBranches');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
                Route::patch('/{id}/set-main', 'setAsMain');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Category Management routes (module-specific categories)
        Route::controller(CategoryController::class)->prefix('categories')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/module/{moduleId}', 'getModuleCategories');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                // Static routes MUST come before dynamic routes
                Route::post('/update-sort-order', 'updateSortOrder');
                
                // Dynamic routes with parameters
                Route::put('/{id}', 'update');
                Route::post('/{id}', 'update'); // POST alternative for file uploads
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Product Management routes (store-specific products)
        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
                Route::post('/{id}/duplicate', 'duplicate');
                Route::post('/import', 'import');
                Route::post('/import-food', 'importFood');
                Route::post('/update-images', 'updateImages');
            });
              Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                // Static routes MUST come before dynamic routes
                Route::post('/reorder', 'reorderProducts');
                Route::post('/images/reorder', 'reorderImages');
                
                // Dynamic routes with parameters
                Route::put('/{id}', 'update');
                Route::post('/{id}', 'update'); // POST alternative for file uploads
                Route::patch('/{id}/toggle-status', 'toggleStatus');
                Route::post('/{id}/images', 'uploadImages');
                Route::delete('/images/{id}', 'deleteImage');
                Route::patch('/images/{id}/set-primary', 'setPrimaryImage');
            });
            
          
        });

        // Option Group Management routes
        Route::controller(OptionGroupController::class)->prefix('option-groups')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/types', 'getTypes');
                Route::get('/makook-sandwitch', 'makookSandwitchList');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Option Value Management routes
        Route::controller(OptionValueController::class)->prefix('option-values')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/group/{groupId}', 'index');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::post('/reorder', 'reorder');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Product Option Management routes (assign options to products)
        Route::controller(ProductOptionController::class)->prefix('product-options')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/product/{productId}', 'index');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/assign-group', 'assignOptionGroup');
                Route::post('/assign-values', 'assignOptionValues');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}', 'updateOptionGroup');
                Route::put('/values/{id}', 'updateOptionValue');
                Route::patch('/values/{id}/stock', 'updateStock');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'removeOptionGroup');
                Route::delete('/values/{id}', 'removeOptionValue');
            });
        });

        // Addon Management routes
        Route::controller(AddonController::class)->prefix('addons')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/categories/{storeId}', 'getCategories');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Product Addon Management routes (assign addons to products)
        Route::controller(ProductAddonController::class)->prefix('product-addons')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/product/{productId}', 'index');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/assign', 'assignAddons');
            });
            
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/product/{productId}/addon/{addonId}', 'updateAddon');
                Route::post('/reorder', 'reorderAddons');
            });
            
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/product/{productId}/addon/{addonId}', 'removeAddon');
            });
        });

        // Activity Log routes (accessible to admins with appropriate permissions)
        Route::controller(ActivityLogController::class)->prefix('activity-logs')->group(function () {
            Route::get('/', 'index');
            Route::get('/stats', 'stats');
            Route::get('/log-names', 'logNames');
            Route::get('/event-types', 'eventTypes');
            Route::get('/{id}', 'show');
            Route::get('/model/{modelType}/{modelId}', 'forModel');
            Route::get('/user/{userType}/{userId}', 'byUser');
            
            // Cleanup route (super_admin only)
            Route::delete('/cleanup', 'cleanup')->middleware('role:super_admin,admins');
        });

        // Get roles list (accessible to all authenticated admins)
        Route::controller(RoleController::class)->prefix('roles')->group(function () {
            Route::get('/list', 'getRolesList');
        });

        // Role Management routes (super_admin only)
        Route::middleware('role:super_admin,admins')->group(function () {
            Route::controller(RoleController::class)->prefix('roles')->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/permissions', 'getPermissions');
                Route::get('/{id}', 'show');
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });

            Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
                Route::get('/', 'index');
                Route::get('/categories', 'getByCategory');
                Route::get('/{id}', 'show');
            });
        });

        // Role Assignment routes (admin-users.roles permission)
        Route::middleware('permission:admin-users.roles,admins')->group(function () {
            Route::controller(RoleAssignmentController::class)->prefix('users')->group(function () {
                Route::get('/{userId}/roles', 'getUserRoles');
                Route::post('/{userId}/roles/assign', 'assignRole');
                Route::post('/{userId}/roles/remove', 'removeRole');
                Route::post('/{userId}/roles/sync', 'syncRoles');
            });
        });

        // Order Management routes
        Route::controller(\App\Http\Controllers\Api\Admin\OrderController::class)->prefix('orders')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::post('/{id}/mark-ready', 'markReadyToPick');
            Route::post('/{id}/cancel', 'cancel');
        });
        // Notification Management routes
        Route::controller(\App\Http\Controllers\Api\Admin\NotificationController::class)->prefix('notifications')->group(function () {
            Route::middleware('permission:notifications.view,admins')->group(function () {
                Route::get('/', 'index');
            });
            
            Route::middleware('permission:notifications.create,admins')->group(function () {
                Route::post('/send', 'send');
            });
        });

        // Vendor Invoice Management routes
        Route::controller(VendorInvoiceController::class)->prefix('vendor-invoices')->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/{id}/pay', 'markAsPaid');
        });

        // Voucher Management routes
        Route::controller(\App\Http\Controllers\Api\Admin\VoucherController::class)->prefix('vouchers')->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Delivery Invoice Management routes
        Route::controller(\App\Http\Controllers\Api\Admin\DeliveryInvoiceController::class)->prefix('delivery-invoices')->group(function () {
            Route::get('/', 'index');
            Route::get('/{id}', 'show');
            Route::post('/{id}/pay', 'markAsPaid');
        });
    });
});
