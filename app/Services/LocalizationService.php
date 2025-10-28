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

    /**
     * Transform localized fields based on current locale
     * Converts name_en/name_ar to name, description_en/description_ar to description
     * 
     * @param array|object $data
     * @param array $fields Fields to localize (e.g., ['name', 'description'])
     * @return array
     */
    public static function localizeFields($data, array $fields = ['name', 'description']): array
    {
        $locale = App::getLocale();
        $result = is_array($data) ? $data : (array) $data;
        
        foreach ($fields as $field) {
            $localizedKey = "{$field}_{$locale}";
            
            // If the localized field exists, use it
            if (array_key_exists($localizedKey, $result)) {
                $result[$field] = $result[$localizedKey];
            }
            
            // Remove all language-specific versions of this field
            foreach (['en', 'ar'] as $lang) {
                $key = "{$field}_{$lang}";
                if (array_key_exists($key, $result)) {
                    unset($result[$key]);
                }
            }
        }
        
        return $result;
    }

    /**
     * Transform nested localized data (e.g., for collections)
     * 
     * @param array $items
     * @param array $fields Fields to localize
     * @return array
     */
    public static function localizeCollection(array $items, array $fields = ['name', 'description']): array
    {
        return array_map(function ($item) use ($fields) {
            if (is_array($item)) {
                // Handle nested arrays (e.g., relationships)
                foreach ($item as $key => $value) {
                    if (is_array($value) && !empty($value)) {
                        // Check if it's a single nested object or array of objects
                        if (isset($value[0])) {
                            // Array of objects
                            $item[$key] = self::localizeCollection($value, $fields);
                        } else {
                            // Single nested object
                            $item[$key] = self::localizeFields($value, $fields);
                        }
                    }
                }
            }
            return self::localizeFields($item, $fields);
        }, $items);
    }
}
