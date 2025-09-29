<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function ($router) {
            Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    require base_path('routes/api/user.php');
                    require base_path('routes/api/store.php');
                    require base_path('routes/api/admin.php');
                    require base_path('routes/api/delivery.php');
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Force JSON responses for API routes
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\SetLanguage::class,
        ]);
        
        // Add JSON response middleware for all requests
        $middleware->append(\App\Http\Middleware\ForceJsonResponse::class);
        
        // Register permission and role middleware
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle authentication exceptions
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.unauthenticated');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'authentication_required'
            ], 401);
        });

        // Handle authorization exceptions
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Access denied. You do not have permission to perform this action.',
                'message_ar' => 'تم رفض الوصول. ليس لديك إذن لتنفيذ هذا الإجراء.',
                'error' => 'access_denied'
            ], 403);
        });

        // Handle validation exceptions
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.validation_failed');
            $firstError = collect($e->errors())->flatten()->first();
            
            return response()->json([
                'success' => false,
                'message' => $firstError ?: $message,
                'data' => null,
                'errors' => $e->errors()
            ], 422);
        });

        // Handle model not found exceptions
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Resource not found.',
                'message_ar' => 'المورد غير موجود.',
                'error' => 'resource_not_found'
            ], 404);
        });

        // Handle method not allowed exceptions
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Method not allowed.',
                'message_ar' => 'الطريقة غير مسموحة.',
                'error' => 'method_not_allowed',
                'allowed_methods' => $e->getHeaders()['Allow'] ?? []
            ], 405);
        });

        // Handle route not found exceptions
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Route not found.',
                'message_ar' => 'المسار غير موجود.',
                'error' => 'route_not_found'
            ], 404);
        });

        // Handle JWT token exceptions
        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Token has expired. Please refresh your token.',
                'message_ar' => 'انتهت صلاحية الرمز المميز. يرجى تحديث الرمز المميز.',
                'error' => 'token_expired'
            ], 401);
        });

        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Token is invalid.',
                'message_ar' => 'الرمز المميز غير صالح.',
                'error' => 'token_invalid'
            ], 401);
        });

        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\JWTException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Token not provided.',
                'message_ar' => 'لم يتم توفير الرمز المميز.',
                'error' => 'token_not_provided'
            ], 401);
        });

        // Handle database connection exceptions
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Database error occurred.',
                'message_ar' => 'حدث خطأ في قاعدة البيانات.',
                'error' => 'database_error'
            ], 500);
        });

        // Handle throttle exceptions
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            return response()->json([
                'success' => false,
                'message_en' => 'Too many requests. Please try again later.',
                'message_ar' => 'طلبات كثيرة جداً. يرجى المحاولة مرة أخرى لاحقاً.',
                'error' => 'too_many_requests',
                'retry_after' => $e->getHeaders()['Retry-After'] ?? null
            ], 429);
        });

        // Handle general exceptions
        $exceptions->render(function (\Throwable $e, $request) {
            // Only show detailed error in development
            $message = app()->environment('local') ? $e->getMessage() : 'An unexpected error occurred.';
            $messageAr = app()->environment('local') ? $e->getMessage() : 'حدث خطأ غير متوقع.';
            
            return response()->json([
                'success' => false,
                'message_en' => $message,
                'message_ar' => $messageAr,
                'error' => 'server_error',
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null
            ], 500);
        });
    })->create();
