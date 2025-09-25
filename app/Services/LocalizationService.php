<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class LocalizationService
{
    private static $messages = [];

    /**
     * Get localized message
     */
    public static function getMessage(string $key, array $replace = []): string
    {
        $locale = App::getLocale();
        
        // Load messages if not already loaded
        if (!isset(self::$messages[$locale])) {
            self::loadMessages($locale);
        }
        
        // Get message using dot notation
        $message = self::getNestedValue(self::$messages[$locale] ?? [], $key);
        
        // If message not found, try English as fallback
        if (!$message && $locale !== 'en') {
            if (!isset(self::$messages['en'])) {
                self::loadMessages('en');
            }
            $message = self::getNestedValue(self::$messages['en'] ?? [], $key);
        }
        
        // If still not found, return the key
        if (!$message) {
            $message = $key;
        }
        
        // Replace placeholders
        foreach ($replace as $placeholder => $value) {
            $message = str_replace(':' . $placeholder, $value, $message);
        }
        
        return $message;
    }

    /**
     * Load messages for a locale
     */
    private static function loadMessages(string $locale): void
    {
        $filePath = resource_path("lang/{$locale}/messages.json");
        
        if (File::exists($filePath)) {
            $content = File::get($filePath);
            self::$messages[$locale] = json_decode($content, true) ?? [];
        } else {
            self::$messages[$locale] = [];
        }
    }

    /**
     * Get nested value using dot notation
     */
    private static function getNestedValue(array $array, string $key): ?string
    {
        $keys = explode('.', $key);
        $value = $array;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return null;
            }
            $value = $value[$k];
        }
        
        return is_string($value) ? $value : null;
    }

    /**
     * Get current locale
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Check if locale is RTL
     */
    public static function isRtl(): bool
    {
        return App::getLocale() === 'ar';
    }
}
