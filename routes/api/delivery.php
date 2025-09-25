<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Delivery\AuthController;

/*
|--------------------------------------------------------------------------
| Delivery API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for delivery personnel.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('delivery')->group(function () {
    // Authentication routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:deliveries');
        Route::post('refresh', 'refresh')->middleware('auth:deliveries');
        Route::get('profile', 'profile')->middleware('auth:deliveries');
    });

    // Protected delivery routes
    Route::middleware('auth:deliveries')->group(function () {
        // Add more delivery-specific routes here
        Route::get('dashboard', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'Delivery Dashboard',
                'message_ar' => 'لوحة تحكم التوصيل',
                'guard' => 'deliveries'
            ]);
        });
        
        Route::get('orders', function () {
            return response()->json([
                'success' => true,
                'message_en' => 'Delivery Orders',
                'message_ar' => 'طلبات التوصيل',
                'guard' => 'deliveries'
            ]);
        });
        
        Route::post('update-availability', function () {
            $user = auth('deliveries')->user();
            $user->availability = !$user->availability;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message_en' => 'Availability updated successfully',
                'message_ar' => 'تم تحديث حالة التوفر بنجاح',
                'availability' => $user->availability
            ]);
        });
    });
});
