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
     * Transform the 'success' key to 'status' (boolean) and convert
     * user status fields from string to boolean in JSON responses
     * for the user guard only.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            // Rename 'success' to 'status' at top level
            if (array_key_exists('success', $data)) {
                $transformed = [];
                foreach ($data as $key => $value) {
                    if ($key === 'success') {
                        $transformed['status'] = (bool) $value;
                    } else {
                        $transformed[$key] = $value;
                    }
                }
                $data = $transformed;
            }

            // Convert status fields from string to boolean in nested data
            $data = $this->transformStatusFields($data);

            $response->setData($data);
        }

        return $response;
    }

    /**
     * Recursively transform 'status' fields from string (active/inactive) to boolean.
     */
    private function transformStatusFields($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($data as $key => $value) {
            if ($key === 'status' && is_string($value)) {
                $data[$key] = $value === 'active';
            } elseif (is_array($value)) {
                $data[$key] = $this->transformStatusFields($value);
            }
        }

        return $data;
    }
}
