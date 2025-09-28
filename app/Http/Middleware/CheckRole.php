<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role, $guard = null): Response
    {
        $guard = $guard ?: config('auth.defaults.guard');
        
        if (!auth($guard)->check()) {
            return response()->json([
                'success' => false,
                'message' => \App\Services\LocalizationService::getMessage('errors.unauthenticated')
            ], 401);
        }

        if (!auth($guard)->user()->hasRole($role)) {
            return response()->json([
                'success' => false,
                'message' => \App\Services\LocalizationService::getMessage('errors.insufficient_role')
            ], 403);
        }

        return $next($request);
    }
}
