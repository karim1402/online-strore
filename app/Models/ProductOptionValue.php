<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOptionValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_option_id',
        'option_value_id',
        'price_type',
        'price_value',
        'stock_quantity',
        'is_available',
    ];

    protected $casts = [
        'price_value' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_available' => 'boolean',
    ];

    /**
     * Get the product option that owns this value
     */
    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    /**
     * Get the option value
     */
    public function optionValue(): BelongsTo
    {
        return $this->belongsTo(OptionValue::class);
    }

    /**
     * Scope to get available values
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('stock_quantity', '>', 0);
    }

    /**
     * Scope to get out of stock values
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /**
     * Check if in stock
     */
    public function inStock($quantity = 1)
    {
        return $this->is_available && $this->stock_quantity >= $quantity;
    }

    /**
     * Decrease stock
     */
    public function decreaseStock($quantity = 1)
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increase stock
     */
    public function increaseStock($quantity = 1)
    {
        $this->increment('stock_quantity', $quantity);
    }

    /**
     * Calculate price based on base price and price type
     */
    public function calculatePrice($basePrice)
    {
        switch ($this->price_type) {
            case 'fixed':
                return $this->price_value;
            case 'additional':
                return $basePrice + $this->price_value;
            case 'percentage':
                return $basePrice * (1 + ($this->price_value / 100));
            default:
                return $basePrice;
        }
    }
}
