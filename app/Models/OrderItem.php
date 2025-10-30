<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Services\LocalizationService;

class OrderItem extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_snapshot',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'product_snapshot' => 'array',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['quantity', 'unit_price', 'total_price'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Order item {$eventName}");
    }

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(OrderItemOption::class);
    }

    public function addons()
    {
        return $this->hasMany(OrderItemAddon::class);
    }

    /**
     * Computed attributes
     */
    public function getProductNameAttribute()
    {
        $locale = LocalizationService::getCurrentLocale();
        return $this->product_snapshot["name_{$locale}"] ?? $this->product_snapshot['name_en'] ?? 'N/A';
    }

    public function getProductDescriptionAttribute()
    {
        $locale = LocalizationService::getCurrentLocale();
        return $this->product_snapshot["description_{$locale}"] ?? $this->product_snapshot['description_en'] ?? '';
    }

    public function getProductImageAttribute()
    {
        return $this->product_snapshot['image_url'] ?? null;
    }

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2) . ' SAR';
    }

    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 2) . ' SAR';
    }

    /**
     * Calculate item price including options and addons
     */
    public function calculateItemPrice()
    {
        $basePrice = $this->product_snapshot['base_price'] ?? 0;
        
        // Add option prices
        $optionsPrice = $this->options->sum(function ($option) {
            return $option->option_snapshot['calculated_price'] ?? 0;
        });

        // Add addon prices
        $addonsPrice = $this->addons->sum('total_price');

        return $basePrice + $optionsPrice + $addonsPrice;
    }
}
