<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['cart_id', 'product_id', 'quantity'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Cart item {$eventName}");
    }

    /**
     * Relationships
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(CartItemOption::class);
    }

    public function addons()
    {
        return $this->hasMany(CartItemAddon::class);
    }

    /**
     * Computed attributes - Price calculation
     */
    public function getItemPriceAttribute()
    {
        if (!$this->product) {
            return '0.00';
        }

        $basePrice = $this->product->offer_price ?? $this->product->base_price;
        $price = $basePrice;

        // Add option prices
        foreach ($this->options as $option) {
            if ($option->productOptionValue) {
                $optionPrice = $option->productOptionValue->calculatePrice($basePrice);
                // For 'fixed' type, add the full price. For others, subtract base to get the difference
                if ($option->productOptionValue->price_type === 'fixed') {
                    $price += $optionPrice;
                } else {
                    $price += ($optionPrice - $basePrice);
                }
            }
        }

        // Add addon prices
        foreach ($this->addons as $addon) {
            if ($addon->addon) {
                $price += ($addon->addon->price * $addon->quantity);
            }
        }

        return number_format($price, 2, '.', '');
    }

    public function getItemTotalAttribute()
    {
        return number_format($this->item_price * $this->quantity, 2, '.', '');
    }
}
