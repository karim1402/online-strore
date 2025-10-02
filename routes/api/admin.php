<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\MainCategoryController;
use App\Http\Controllers\Api\Admin\StoreController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Controllers\Api\Admin\RoleAssignmentController;

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
        
        Route::get('users', function () {
            $message = \App\Services\LocalizationService::getMessage('guards.admin_dashboard');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => ['info' => 'All Users Management'],
                'guard' => 'admins'
            ]);
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

        // Main Categories CRUD routes (permission-based)
        Route::controller(MainCategoryController::class)->prefix('main-categories')->group(function () {
            Route::middleware('permission:categories.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/active', 'getActiveCategories');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:categories.create,admins')->group(function () {
                Route::post('/', 'store');
            });
            
            Route::middleware('permission:categories.update,admins')->group(function () {
                Route::put('/{id}', 'update');
                Route::patch('/{id}/toggle-status', 'toggleStatus');
            });
            
            Route::middleware('permission:categories.delete,admins')->group(function () {
                Route::delete('/{id}', 'destroy');
            });
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

        // Store Management routes (permission-based)
        Route::controller(StoreController::class)->prefix('stores')->group(function () {
            Route::middleware('permission:stores.view,admins')->group(function () {
                Route::get('/', 'index');
                Route::get('/pending', 'getPendingStores');
                Route::get('/{id}', 'show');
            });
            
            Route::middleware('permission:stores.create,admins')->group(function () {
                Route::post('/', 'store');
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
    });
});
