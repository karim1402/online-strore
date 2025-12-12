<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\LocalizationService;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItemOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'option_snapshot',
    ];

    protected $casts = [
        'option_snapshot' => 'array',
    ];

    public $timestamps = false;

    /**
     * Relationships
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Computed attributes
     */
    public function getOptionGroupNameAttribute()
    {
        $locale = LocalizationService::getCurrentLocale();
        return $this->option_snapshot["option_group_name_{$locale}"] 
            ?? $this->option_snapshot['option_group_name_en'] 
            ?? 'N/A';
    }

    public function getOptionValueNameAttribute()
    {
        $locale = LocalizationService::getCurrentLocale();
        return $this->option_snapshot["option_value_name_{$locale}"] 
            ?? $this->option_snapshot['option_value_name_en'] 
            ?? 'N/A';
    }

    public function getCalculatedPriceAttribute()
    {
        return $this->option_snapshot['calculated_price'] ?? 0;
    }

    public function getFormattedPriceAttribute()
    {
        $price = $this->calculated_price;
        return $price > 0 ? '+' . number_format($price, 2) . ' SAR' : '';
    }
}
