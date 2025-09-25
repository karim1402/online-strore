<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Default API route
Route::get('/', function () {
    $message = \App\Services\LocalizationService::getMessage('success.api_running');
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => [
            'version' => '1.0.0',
        'guards' => [
            'user' => '/api/user',
            'store' => '/api/store', 
            'admin' => '/api/admin',
            'delivery' => '/api/delivery'
        ],
        'endpoints' => [
            'user' => [
                'register' => 'POST /api/user/register',
                'login' => 'POST /api/user/login',
                'profile' => 'GET /api/user/profile',
                'logout' => 'POST /api/user/logout',
                'refresh' => 'POST /api/user/refresh',
                'dashboard' => 'GET /api/user/dashboard',
                'test' => 'GET /api/user/test'
            ],
            'store' => [
                'register' => 'POST /api/store/register',
                'login' => 'POST /api/store/login',
                'profile' => 'GET /api/store/profile',
                'logout' => 'POST /api/store/logout',
                'refresh' => 'POST /api/store/refresh',
                'dashboard' => 'GET /api/store/dashboard'
            ],
            'admin' => [
                'register' => 'POST /api/admin/register',
                'login' => 'POST /api/admin/login',
                'profile' => 'GET /api/admin/profile',
                'logout' => 'POST /api/admin/logout',
                'refresh' => 'POST /api/admin/refresh',
                'dashboard' => 'GET /api/admin/dashboard'
            ],
            'delivery' => [
                'register' => 'POST /api/delivery/register',
                'login' => 'POST /api/delivery/login',
                'profile' => 'GET /api/delivery/profile',
                'logout' => 'POST /api/delivery/logout',
                'refresh' => 'POST /api/delivery/refresh',
                'dashboard' => 'GET /api/delivery/dashboard'
            ]
        ]
        ]
    ]);
});

// Health check
Route::get('/health', function () {
    $message = \App\Services\LocalizationService::getMessage('success.api_healthy');
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => [
            'status' => 'healthy',
            'timestamp' => now(),
            'server_time' => now()->toDateTimeString(),
            'timezone' => config('app.timezone'),
            'locale' => \App\Services\LocalizationService::getCurrentLocale()
        ]
    ]);
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    $message = \App\Services\LocalizationService::getMessage('errors.endpoint_not_found');
    return response()->json([
        'success' => false,
        'message' => $message,
        'data' => null,
        'error' => 'endpoint_not_found',
        'available_endpoints' => [
            'GET /api/' => 'API information',
            'GET /api/health' => 'Health check',
            'POST /api/user/register' => 'User registration',
            'POST /api/user/login' => 'User login',
            'GET /api/user/test' => 'User API test'
        ]
    ], 404);
});
