<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get language from header (Accept-Language or X-Language)
        $language = $request->header('Accept-Language', 'en');
        $customLang = $request->header('X-Language');
        
        // Priority: X-Language header > Accept-Language header > default 'en'
        if ($customLang) {
            $language = $customLang;
        }
        
        // Extract language code (handle cases like 'en-US', 'ar-SA')
        $language = strtolower(substr($language, 0, 2));
        
        // Supported languages
        $supportedLanguages = ['en', 'ar'];
        
        // Default to 'en' if language is not supported
        if (!in_array($language, $supportedLanguages)) {
            $language = 'en';
        }
        
        // Set application locale
        App::setLocale($language);
        
        // Store in request for easy access
        $request->attributes->set('locale', $language);
        
        return $next($request);
    }
}
