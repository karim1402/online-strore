<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\Admin\AuthController;
use App\Http\Controllers\Api\V2\Admin\AdminUserController;
use App\Http\Controllers\Api\V2\Admin\ModuleController;
use App\Http\Controllers\Api\V2\Admin\StoreController;
use App\Http\Controllers\Api\V2\Admin\RoleController;
use App\Http\Controllers\Api\V2\Admin\PermissionController;
use App\Http\Controllers\Api\V2\Admin\RoleAssignmentController;
use App\Http\Controllers\Api\V2\Admin\BranchController;
use App\Http\Controllers\Api\V2\Admin\CategoryController;
use App\Http\Controllers\Api\V2\Admin\ProductController;
use App\Http\Controllers\Api\V2\Admin\OptionGroupController;
use App\Http\Controllers\Api\V2\Admin\OptionValueController;
use App\Http\Controllers\Api\V2\Admin\ProductOptionController;
use App\Http\Controllers\Api\V2\Admin\AddonController;
use App\Http\Controllers\Api\V2\Admin\ProductAddonController;
use App\Http\Controllers\Api\V2\Admin\ActivityLogController;
use App\Http\Controllers\Api\V2\Admin\DeliveryUserController;
use App\Http\Controllers\Api\V2\Admin\UserController;
use App\Http\Controllers\Api\V2\Admin\VendorInvoiceController;
use App\Http\Controllers\Api\V2\Admin\AppSettingController;
use App\Http\Controllers\Api\V2\Admin\FeaturedSectionController;

/*
|--------------------------------------------------------------------------
| Admin API V2 Routes
|--------------------------------------------------------------------------
|
| All routes here are served under /api/v2/admin/*.
| v1 routes (/api/admin/*) are NOT changed.
|
*/

Route::prefix('admin')->group(function () {

    // Authentication
    Route::controller(AuthController::class)->group(function () {
        Route::post('login',      'login');
        Route::post('logout',     'logout')->middleware('auth:admins');
        Route::post('refresh',    'refresh')->middleware('auth:admins');
        Route::get('profile',     'profile')->middleware('auth:admins');
        Route::put('profile',     'updateProfile')->middleware('auth:admins');
        Route::post('fcm-token',  'updateFcmToken')->middleware('auth:admins');
    });

    // Protected admin routes
    Route::middleware('auth:admins')->group(function () {

        Route::get('dashboard', function () {
            $message = \App\Services\LocalizationService::getMessage('guards.admin_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => null,
                'guard'   => 'admins',
                'version' => 'v2',
            ]);
        });

        // Dashboard Statistics
        Route::controller(\App\Http\Controllers\Api\V2\Admin\DashboardController::class)->prefix('dashboard')->group(function () {
            Route::get('/stats',               'stats');
            Route::get('/orders-distribution', 'ordersDistribution');
            Route::get('/revenue-chart',       'revenueChart');
            Route::get('/module-performance',  'modulePerformance');
            Route::get('/recent-orders',       'recentOrders');
        });

        // Reports
        Route::controller(\App\Http\Controllers\Api\V2\Admin\ReportsController::class)->prefix('reports')->group(function () {
            Route::get('/export', 'export');

            Route::get('/revenue/total',              'totalRevenue');
            Route::get('/revenue/over-time',          'revenueOverTime');
            Route::get('/revenue/by-module',          'revenueByModule');
            Route::get('/revenue/by-category',        'revenueByCategory');
            Route::get('/revenue/by-store',           'revenueByStore');
            Route::get('/revenue/by-payment-method',  'revenueByPaymentMethod');
            Route::get('/revenue/discounts-impact',   'discountImpact');

            Route::get('/sales/revenue-trend',        'revenueTrend');
            Route::get('/sales/payment-methods',      'paymentMethodsBreakdown');

            Route::get('/orders/items-summary',       'itemsSummary');
            Route::get('/orders/by-store',            'ordersByStore');
            Route::get('/orders/by-module',           'ordersByModule');
            Route::get('/orders/per-day',             'ordersPerDay');
            Route::get('/orders/distribution',        'orderDistribution');
            Route::get('/orders/cancellations',       'orderCancellations');
            Route::get('/orders/average-value',       'averageOrderValue');
            Route::get('/orders/admin-created',       'adminCreatedOrders');

            Route::get('/products/top-selling',       'topSellingProducts');
            Route::get('/products/most-viewed',       'mostViewedProducts');
            Route::get('/products/best-sellers',      'bestSellersFlagged');
            Route::get('/products/with-offers',       'productsWithOffers');
            Route::get('/products/{id}/buyers',       'productBuyers');
            Route::get('/products/{id}/orders',       'productOrders');

            Route::get('/users/growth',               'userGrowth');
            Route::get('/users/top-customers',        'topCustomers');
            Route::get('/users/inactive',             'inactiveUsers');

            Route::get('/delivery/driver-performance','driverPerformance');
            Route::get('/delivery/availability',      'driverAvailability');
            Route::get('/delivery/deliveries-per-day','deliveriesPerDay');
            Route::get('/delivery/delivery-vs-pickup','deliveryVsPickup');

            Route::get('/vouchers/usage',                           'voucherUsageAndEffectiveness');
            Route::get('/vouchers/{voucher_id}/users',              'voucherUsers');
            Route::get('/vouchers/{voucher_id}/users/{user_id}/orders', 'voucherUserOrders');

            Route::get('/payments/success-rate',      'paymentSuccessRate');
            Route::get('/payments/merchant-fees',     'merchantFees');
            Route::get('/payments/monthly-trend',     'monthlyPaymentTrend');

            Route::get('/stores/status-overview',     'storeStatusOverview');

            Route::get('/attribution/campaign-performance', 'campaignPerformance');
            Route::get('/attribution/source-performance',   'sourcePerformance');
        });

        // Users
        Route::controller(UserController::class)->prefix('users')->group(function () {
            Route::middleware('permission:users.view,admins')->group(function () {
                Route::get('/',                    'index');
                Route::get('/export',              'export');
                Route::get('/deleted',             'deletedIndex');
                Route::get('/{id}',       'show');
                Route::get('/{id}/orders','userOrders');
            });
            Route::middleware('permission:users.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:users.update,admins')->group(function () {
                Route::put('/{id}',                'update');
                Route::patch('/{id}/toggle-status','toggleStatus');
                Route::patch('/{id}/restore',      'restore');
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
                'data'    => ['info' => 'System Settings'],
                'guard'   => 'admins',
            ]);
        });

        // Modules
        Route::controller(ModuleController::class)->prefix('modules')->group(function () {
            Route::middleware('permission:categories.view,admins')->group(function () {
                Route::get('/',        'index');
                Route::get('/active',  'getActiveModules');
                Route::get('/{id}',    'show');
            });
            Route::middleware('permission:categories.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:categories.update,admins')->group(function () {
                Route::put('/{id}',                 'update');
                Route::post('/{id}',                'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            Route::middleware('permission:categories.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Module Ads
        Route::controller(\App\Http\Controllers\Api\V2\Admin\ModuleAdController::class)->prefix('module-ads')->group(function () {
            Route::middleware('permission:categories.view,admins')->group(function () {
                Route::get('/',     'index');
                Route::get('/{id}', 'show');
            });
            Route::middleware('permission:categories.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:categories.update,admins')->group(function () {
                Route::put('/{id}',                 'update');
                Route::post('/{id}',                'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            Route::middleware('permission:categories.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Home Ads
        Route::controller(\App\Http\Controllers\Api\V2\Admin\HomeAdController::class)->prefix('home-ads')->group(function () {
            Route::get('/',                      'index');
            Route::post('/',                     'store');
            Route::get('/{id}',                  'show');
            Route::put('/{id}',                  'update');
            Route::delete('/{id}',               'destroy');
            Route::patch('/{id}/toggle-status',  'toggleStatus');
        });

        // Splash Ads
        Route::controller(\App\Http\Controllers\Api\V2\Admin\SplashAdController::class)->prefix('splash-ads')->group(function () {
            Route::get('/',                      'index');
            Route::post('/',                     'store');
            Route::get('/{id}',                  'show');
            Route::put('/{id}',                  'update');
            Route::post('/{id}',                 'update');
            Route::delete('/{id}',               'destroy');
            Route::patch('/{id}/toggle-status',  'toggleStatus');
        });

        // Admin Users
        Route::controller(AdminUserController::class)->prefix('admin-users')->group(function () {
            Route::middleware('permission:admin-users.view,admins')->group(function () {
                Route::get('/',     'index');
                Route::get('/{id}', 'show');
            });
            Route::middleware('permission:admin-users.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:admin-users.update,admins')->group(function () {
                Route::put('/{id}',                 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            Route::middleware('permission:admin-users.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Delivery Users
        Route::controller(DeliveryUserController::class)->prefix('delivery-users')->group(function () {
            Route::middleware('permission:delivery-users.view,admins')->group(function () {
                Route::get('/',     'index');
                Route::get('/{id}', 'show');
            });
            Route::middleware('permission:delivery-users.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:delivery-users.update,admins')->group(function () {
                Route::put('/{id}',                 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            Route::middleware('permission:delivery-users.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Stores
        Route::controller(StoreController::class)->prefix('stores')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/',          'index');
                Route::get('/all',       'getAll');
                Route::get('/pending',   'getPendingStores');
                Route::get('/{id}',      'show');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}',  'update');
                Route::post('/{id}', 'update');
            });
            Route::middleware('permission:stores.approve,admins')->group(function () {
                Route::post('/{id}/approve',         'approve');
                Route::post('/{id}/reject',          'reject');
                Route::post('/{id}/suspend',         'suspend');
                Route::post('/{id}/reactivate',      'reactivate');
                Route::patch('/{id}/toggle-status',  'toggleStatus');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Branches
        Route::controller(BranchController::class)->prefix('branches')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/',                    'index');
                Route::get('/store/{storeId}',     'getStoreBranches');
                Route::get('/{id}',                'show');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}',                 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
                Route::patch('/{id}/set-main',      'setAsMain');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Categories
        Route::controller(CategoryController::class)->prefix('categories')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/',                    'index');
                Route::get('/module/{moduleId}',   'getModuleCategories');
                Route::get('/{id}',                'show');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::post('/update-sort-order',   'updateSortOrder');
                Route::put('/{id}',                 'update');
                Route::post('/{id}',                'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Products
        Route::controller(ProductController::class)->prefix('products')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/',         'index');
                Route::get('/export',   'export');
                Route::get('/{id}',     'show');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/',                       'store');
                Route::post('/{id}/duplicate',         'duplicate');
                Route::post('/import',                 'import');
                Route::post('/import-check-missing',   'checkMissingProducts');
                Route::post('/import-food',            'importFood');
                Route::post('/update-images',          'updateImages');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::post('/reorder',                  'reorderProducts');
                Route::post('/images/reorder',           'reorderImages');
                Route::put('/{id}',                      'update');
                Route::post('/{id}',                     'update');
                Route::patch('/{id}/toggle-status',      'toggleStatus');
                Route::post('/{id}/images',              'uploadImages');
                Route::delete('/images/{id}',            'deleteImage');
                Route::patch('/images/{id}/set-primary', 'setPrimaryImage');
            });
        });

        // Option Groups
        Route::controller(OptionGroupController::class)->prefix('option-groups')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/',                    'index');
                Route::get('/types',               'getTypes');
                Route::get('/makook-sandwitch',    'makookSandwitchList');
                Route::get('/{id}',                'show');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}',                 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Option Values
        Route::controller(OptionValueController::class)->prefix('option-values')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/group/{groupId}', 'index');
                Route::get('/{id}',            'show');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}',      'update');
                Route::post('/reorder',  'reorder');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Product Options
        Route::controller(ProductOptionController::class)->prefix('product-options')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/product/{productId}', 'index');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/assign-group',  'assignOptionGroup');
                Route::post('/assign-values', 'assignOptionValues');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}',                  'updateOptionGroup');
                Route::put('/values/{id}',           'updateOptionValue');
                Route::patch('/values/{id}/stock',   'updateStock');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}',         'removeOptionGroup');
                Route::delete('/values/{id}',  'removeOptionValue');
            });
        });

        // Addons
        Route::controller(AddonController::class)->prefix('addons')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/',                      'index');
                Route::get('/categories/{storeId}',  'getCategories');
                Route::get('/{id}',                  'show');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/{id}',                 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
        });

        // Product Addons
        Route::controller(ProductAddonController::class)->prefix('product-addons')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/product/{productId}', 'index');
            });
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/assign',             'assignAddons');
                Route::post('/assign-to-module',   'assignAddonsToModule');
            });
            Route::middleware('permission:stores.update,admins')->group(function () {
                Route::put('/product/{productId}/addon/{addonId}', 'updateAddon');
                Route::post('/reorder',                            'reorderAddons');
            });
            Route::middleware('permission:stores.delete,admins')->group(function () {
                Route::delete('/product/{productId}/addon/{addonId}', 'removeAddon');
                Route::delete('/remove-from-module',                  'removeAddonsFromModule');
            });
        });

        // Activity Logs
        Route::controller(ActivityLogController::class)->prefix('activity-logs')->group(function () {
            Route::get('/',                              'index');
            Route::get('/stats',                         'stats');
            Route::get('/log-names',                     'logNames');
            Route::get('/event-types',                   'eventTypes');
            Route::get('/{id}',                          'show');
            Route::get('/model/{modelType}/{modelId}',   'forModel');
            Route::get('/user/{userType}/{userId}',      'byUser');
            Route::delete('/cleanup', 'cleanup')->middleware('role:super_admin,admins');
        });

        // Roles (list — all admins)
        Route::controller(RoleController::class)->prefix('roles')->group(function () {
            Route::get('/list', 'getRolesList');
        });

        // Roles & Permissions (super_admin only)
        Route::middleware('role:super_admin,admins')->group(function () {
            Route::controller(RoleController::class)->prefix('roles')->group(function () {
                Route::get('/',               'index');
                Route::post('/',              'store');
                Route::get('/permissions',    'getPermissions');
                Route::get('/{id}',           'show');
                Route::put('/{id}',           'update');
                Route::delete('/{id}',        'destroy');
            });

            Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
                Route::get('/',             'index');
                Route::get('/categories',   'getByCategory');
                Route::get('/{id}',         'show');
            });
        });

        // Role Assignment
        Route::middleware('permission:admin-users.roles,admins')->group(function () {
            Route::controller(RoleAssignmentController::class)->prefix('users')->group(function () {
                Route::get('/{userId}/roles',          'getUserRoles');
                Route::post('/{userId}/roles/assign',  'assignRole');
                Route::post('/{userId}/roles/remove',  'removeRole');
                Route::post('/{userId}/roles/sync',    'syncRoles');
            });
        });

        // Orders
        Route::controller(\App\Http\Controllers\Api\V2\Admin\OrderController::class)->prefix('orders')->group(function () {
            Route::get('/statistics',           'statistics');
            Route::get('/',                     'index');
            Route::post('/',                    'store');
            Route::get('/{id}',                 'show');
            Route::post('/{id}/mark-ready',     'markReadyToPick');
            Route::post('/{id}/mark-delivered', 'markDelivered');
            Route::post('/{id}/cancel',         'cancel');
        });

        // Notifications
        Route::controller(\App\Http\Controllers\Api\V2\Admin\NotificationController::class)->prefix('notifications')->group(function () {
            Route::middleware('permission:notifications.view,admins')->group(function () {
                Route::get('/', 'index');
            });
            Route::middleware('permission:notifications.create,admins')->group(function () {
                Route::post('/send', 'send');
            });
        });

        // Vendor Invoices
        Route::controller(VendorInvoiceController::class)->prefix('vendor-invoices')->group(function () {
            Route::get('/',          'index');
            Route::get('/{id}',      'show');
            Route::post('/{id}/pay', 'markAsPaid');
        });

        // Vouchers
        Route::controller(\App\Http\Controllers\Api\V2\Admin\VoucherController::class)->prefix('vouchers')->group(function () {
            Route::get('/',      'index');
            Route::post('/',     'store');
            Route::get('/{id}',  'show');
            Route::put('/{id}',  'update');
            Route::delete('/{id}', 'destroy');
        });

        // Delivery Invoices
        Route::controller(\App\Http\Controllers\Api\V2\Admin\DeliveryInvoiceController::class)->prefix('delivery-invoices')->group(function () {
            Route::get('/',          'index');
            Route::get('/{id}',      'show');
            Route::post('/{id}/pay', 'markAsPaid');
        });

        // App Settings
        Route::controller(AppSettingController::class)->prefix('app-settings')->group(function () {
            Route::get('/working-hours',             'getWorkingHours');
            Route::put('/working-hours',             'updateWorkingHours');
            Route::get('/app-version/{platform}',    'getAppVersion');
            Route::put('/app-version/{platform}',    'updateAppVersion');
            Route::get('/delivery-settings',         'getDeliverySettings');
            Route::put('/delivery-settings',         'updateDeliverySettings');
        });

        // Featured Sections
        Route::controller(FeaturedSectionController::class)->prefix('featured-sections')->group(function () {
            Route::get('/',                      'index');
            Route::post('/',                     'store');
            Route::get('/available-items',       'getAvailableItems');
            Route::get('/{id}',                  'show');
            Route::put('/{id}',                  'update');
            Route::delete('/{id}',               'destroy');
            Route::patch('/{id}/toggle-status',  'toggleStatus');
            Route::post('/update-sort-order',    'updateSortOrder');
        });
    });

    // Public export (no auth required)
    Route::get('users/export-low-orders', [UserController::class, 'exportLowOrderUsers']);
});
