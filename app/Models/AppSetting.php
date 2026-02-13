<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AppSetting extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'app_settings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['key', 'value'];

    /**
     * Egypt timezone constant.
     */
    const TIMEZONE = 'Africa/Cairo';

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key (create or update).
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get the working hours (opening and closing time).
     */
    public static function getWorkingHours(): array
    {
        return [
            'opening_time' => static::get('opening_time', '09:00'),
            'closing_time' => static::get('closing_time', '23:00'),
        ];
    }

    /**
     * Check if the app is currently open based on working hours.
     * Uses Egypt timezone (Africa/Cairo).
     */
    public static function isOpen(): bool
    {
        $hours = static::getWorkingHours();

        $openingTime = $hours['opening_time'];
        $closingTime = $hours['closing_time'];

        if (is_null($openingTime) || is_null($closingTime)) {
            return true; // If no times set, assume always open
        }

        $now = Carbon::now(self::TIMEZONE);
        $currentTime = $now->format('H:i');

        // Handle overnight hours (e.g., opening 22:00, closing 02:00)
        if ($closingTime < $openingTime) {
            return $currentTime >= $openingTime || $currentTime <= $closingTime;
        }

        return $currentTime >= $openingTime && $currentTime <= $closingTime;
    }
}
