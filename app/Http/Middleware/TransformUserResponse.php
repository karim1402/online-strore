<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransformUserResponse
{
    /**
     * Handle an incoming request.
     *
     * Transform the 'success' key to 'status' (boolean) in JSON responses
     * for the user guard only.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            if (array_key_exists('success', $data)) {
                // Create new array with 'status' instead of 'success', preserving key order
                $transformed = [];
                foreach ($data as $key => $value) {
                    if ($key === 'success') {
                        $transformed['status'] = (bool) $value;
                    } else {
                        $transformed[$key] = $value;
                    }
                }
                $response->setData($transformed);
            }
        }

        return $response;
    }
}
