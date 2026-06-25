<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ForwardToLiveServer
{
    private const LIVE_BASE_URL = 'https://mainmak.devdigitalvibes.com/public';

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }

        if ($response->isSuccessful() || $response->status() === 201) {
            $this->forwardRequest($request);
        }

        return $response;
    }

    private function forwardRequest(Request $request): void
    {
        try {
            $liveUrl = self::LIVE_BASE_URL . '/' . $request->path();
            $method = strtolower($request->method());

            $httpRequest = Http::timeout(15)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Accept-Language' => app()->getLocale(),
                ]);

            if ($request->bearerToken()) {
                $httpRequest = $httpRequest->withToken($request->bearerToken());
            }

            $liveResponse = $httpRequest->$method($liveUrl, $request->all());

            Log::info('Forwarded to live server', [
                'method' => $request->method(),
                'url' => $liveUrl,
                'status' => $liveResponse->status(),
                'response' => $liveResponse->json(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to forward to live server', [
                'method' => $request->method(),
                'url' => $liveUrl ?? $request->path(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
