<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission, $guard = null): Response
    {
        $guard = $guard ?: config('auth.defaults.guard');
        
        if (!auth($guard)->check()) {
            return response()->json([
                'success' => false,
                'message' => \App\Services\LocalizationService::getMessage('errors.unauthenticated'),
                'data' => null,
                'error' => 'authentication_required'
            ], 401);
        }

        if (!auth($guard)->user()->can($permission)) {
            return response()->json([
                'success' => false,
                'message' => \App\Services\LocalizationService::getMessage('errors.insufficient_permissions'),
                'data' => null,
                'error' => 'insufficient_permissions'
            ], 403);
        }

        return $next($request);
    }
}
