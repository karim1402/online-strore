<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address_name',
        'address_type',
        'building_name',
        'apartment_number',
        'floor_number',
        'street_name',
        'landmark',
        'phone',
        'latitude',
        'longitude',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full address as a single string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->building_name,
            $this->apartment_number ? "Apt {$this->apartment_number}" : null,
            $this->floor_number ? "Floor {$this->floor_number}" : null,
            $this->street_name,
            $this->landmark ? "Near {$this->landmark}" : null,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Scope a query to only include default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query by address type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('address_type', $type);
    }

    /**
     * Configure activity logging options.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'address_name',
                'address_type',
                'building_name',
                'apartment_number',
                'floor_number',
                'street_name',
                'landmark',
                'phone',
                'latitude',
                'longitude',
                'is_default'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User address {$eventName}")
            ->useLogName('user_address');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When setting an address as default, unset other default addresses for the same user
        static::saving(function ($address) {
            if ($address->is_default && $address->user_id) {
                static::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Calculate delivery fee for this address
     */
    public function calculateDeliveryFee(): float
    {
        $baseFee = (float) \App\Models\AppSetting::get('delivery_base_fee', '0');
        $kmFee   = (float) \App\Models\AppSetting::get('delivery_km_fee', '0');
        $startLat = \App\Models\AppSetting::get('delivery_start_lat');
        $startLng = \App\Models\AppSetting::get('delivery_start_lng');

        $totalFee = $baseFee;

        if ($startLat !== null && $startLng !== null && $this->latitude !== null && $this->longitude !== null) {
            $earthRadius = 6371; // Earth's radius in kilometers

            $latFrom = deg2rad((float)$startLat);
            $lonFrom = deg2rad((float)$startLng);
            $latTo = deg2rad((float)$this->latitude);
            $lonTo = deg2rad((float)$this->longitude);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $a = sin($latDelta / 2) * sin($latDelta / 2) +
                 cos($latFrom) * cos($latTo) *
                 sin($lonDelta / 2) * sin($lonDelta / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            $distanceKm = max(1, round($earthRadius * $c));
            
            // Base fee + (Distance in km * Km Fee)
            $totalFee += ($distanceKm * $kmFee);
        }

        return round($totalFee, 2);
    }
}
