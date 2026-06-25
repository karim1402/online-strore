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
            // ── v1 routes (unchanged) ──────────────────────────────────
            Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    require base_path('routes/api/user.php');
                    require base_path('routes/api/store.php');
                    require base_path('routes/api/admin.php');
                    require base_path('routes/api/delivery.php');
                });

            // ── v2 routes ──────────────────────────────────────────────
            Route::middleware('api')
                ->prefix('api/v2')
                ->group(function () {
                    require base_path('routes/api/v2/user.php');
                    require base_path('routes/api/v2/admin.php');
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
            'forward.live' => \App\Http\Middleware\ForwardToLiveServer::class,
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
            $message = \App\Services\LocalizationService::getMessage('errors.access_denied');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
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
            $message = \App\Services\LocalizationService::getMessage('errors.resource_not_found');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'resource_not_found'
            ], 404);
        });

        // Handle method not allowed exceptions
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.method_not_allowed');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'method_not_allowed',
                'allowed_methods' => $e->getHeaders()['Allow'] ?? []
            ], 405);
        });

        // Handle route not found exceptions
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.route_not_found');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'route_not_found'
            ], 404);
        });

        // Handle JWT token exceptions
        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.token_expired');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'token_expired'
            ], 401);
        });

        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.token_invalid');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'token_invalid'
            ], 401);
        });

        $exceptions->render(function (\Tymon\JWTAuth\Exceptions\JWTException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.token_not_provided');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'token_not_provided'
            ], 401);
        });

        // Handle database connection exceptions
        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.database_error');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'database_error'
            ], 500);
        });

        // Handle throttle exceptions
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            $message = \App\Services\LocalizationService::getMessage('errors.too_many_requests');
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'too_many_requests',
                'retry_after' => $e->getHeaders()['Retry-After'] ?? null
            ], 429);
        });

        // Handle general exceptions
        $exceptions->render(function (\Throwable $e, $request) {
            // Only show detailed error in development
            if (app()->environment('local')) {
                $message = $e->getMessage();
            } else {
                $message = \App\Services\LocalizationService::getMessage('errors.server_error');
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => null,
                'error' => 'server_error',
                'trace' => app()->environment('local') ? $e->getTraceAsString() : null
            ], 500);
        });
    })->create();