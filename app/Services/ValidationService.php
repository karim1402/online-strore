<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidationService
{
    /**
     * Create a validator with localized messages
     */
    public static function make(array $data, array $rules, array $customMessages = []): \Illuminate\Validation\Validator
    {
        // Get localized validation messages
        $messages = self::getLocalizedMessages();
        
        // Get localized attribute names
        $attributes = self::getLocalizedAttributes();
        
        // Merge custom messages with localized ones
        $allMessages = array_merge($messages, $customMessages);
        
        return Validator::make($data, $rules, $allMessages, $attributes);
    }

    /**
     * Validate data and throw exception if fails
     */
    public static function validate(array $data, array $rules, array $customMessages = []): array
    {
        $validator = self::make($data, $rules, $customMessages);
        
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        
        return $validator->validated();
    }

    /**
     * Get localized validation messages
     */
    private static function getLocalizedMessages(): array
    {
        $messages = [];
        $validationRules = [
            'required', 'email', 'min', 'max', 'confirmed', 'unique', 
            'string', 'between', 'in', 'numeric', 'integer', 'boolean', 
            'date', 'image', 'mimes', 'size', 'regex'
        ];
        
        foreach ($validationRules as $rule) {
            $messages[$rule] = LocalizationService::getMessage("validation.{$rule}");
        }
        
        return $messages;
    }

    /**
     * Get localized attribute names
     */
    private static function getLocalizedAttributes(): array
    {
        $attributes = [];
        $attributeKeys = [
            'name', 'email', 'password', 'password_confirmation', 'store_name',
            'phone', 'address', 'role', 'vehicle_type', 'vehicle_number',
            'license_number', 'availability', 'status'
        ];
        
        foreach ($attributeKeys as $key) {
            $attributes[$key] = LocalizationService::getMessage("attributes.{$key}");
        }
        
        return $attributes;
    }

    /**
     * Format validation errors for API response
     */
    public static function formatErrors(\Illuminate\Validation\Validator $validator): array
    {
        $errors = [];
        
        foreach ($validator->errors()->messages() as $field => $messages) {
            $errors[$field] = $messages;
        }
        
        return $errors;
    }

    /**
     * Get first validation error message
     */
    public static function getFirstError(\Illuminate\Validation\Validator $validator): string
    {
        return $validator->errors()->first();
    }
}
