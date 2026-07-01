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

    private const ZONE_CENTER_LAT     = 30.79065887518099;
    private const ZONE_CENTER_LNG     = 30.99946975708008;
    private const INNER_ZONE_RADIUS_M = 2691.967667786481;
    private const OUTER_ZONE_RADIUS_M = 3513.1458918722105;

    /**
     * Calculate delivery fee for this address
     *
     * @param float $subtotal
     * @param \Illuminate\Support\Collection|null $cartItems  Cart items with loaded product
     */
    public function calculateDeliveryFee(float $subtotal = 0, $cartItems = null): float
    {
        if ($cartItems && \App\Models\Voucher::cartHasRestrictedProducts($cartItems)) {
            return 10.00;
        }

        // Outer zone always pays flat 35.00 regardless of subtotal or order history
        if ($this->latitude !== null && $this->longitude !== null) {
            $distanceMeters = $this->haversineMeters(
                self::ZONE_CENTER_LAT, self::ZONE_CENTER_LNG,
                (float) $this->latitude, (float) $this->longitude
            );

            if ($distanceMeters > self::INNER_ZONE_RADIUS_M) {
                return 35.00;
            }
        }

        // Inner zone: normal fee logic
        if ($subtotal > 149) {
            return 0.00;
        }

        if ($this->user_id) {
            $deliveredOrdersCount = \App\Models\Order::where('user_id', $this->user_id)->count();

            if ($deliveredOrdersCount < 1 && $subtotal > 100) {
                return 0.00;
            }
        }

        $baseFee  = (float) \App\Models\AppSetting::get('delivery_base_fee', '0');
        $kmFee    = (float) \App\Models\AppSetting::get('delivery_km_fee', '0');
        $startLat = \App\Models\AppSetting::get('delivery_start_lat');
        $startLng = \App\Models\AppSetting::get('delivery_start_lng');

        $totalFee = $baseFee;

        if ($startLat !== null && $startLng !== null && $this->latitude !== null && $this->longitude !== null) {
            $distanceKm = max(1, round($this->haversineMeters(
                (float) $startLat, (float) $startLng,
                (float) $this->latitude, (float) $this->longitude
            ) / 1000));

            $totalFee += ($distanceKm * $kmFee);
        }

        return round($totalFee, 2);
    }

    private function haversineMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $latFrom     = deg2rad($lat1);
        $lonFrom     = deg2rad($lng1);
        $latTo       = deg2rad($lat2);
        $lonTo       = deg2rad($lng2);
        $latDelta    = $latTo - $latFrom;
        $lonDelta    = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;

        return 2 * $earthRadius * atan2(sqrt($a), sqrt(1 - $a));
    }
}
