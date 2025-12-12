<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\LocalizationService;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItemAddon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'addon_snapshot',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'addon_snapshot' => 'array',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
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
    public function getAddonNameAttribute()
    {
        $locale = LocalizationService::getCurrentLocale();
        return $this->addon_snapshot["name_{$locale}"] 
            ?? $this->addon_snapshot['name_en'] 
            ?? 'N/A';
    }

    public function getAddonDescriptionAttribute()
    {
        $locale = LocalizationService::getCurrentLocale();
        return $this->addon_snapshot["description_{$locale}"] 
            ?? $this->addon_snapshot['description_en'] 
            ?? '';
    }

    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2) . ' SAR';
    }

    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 2) . ' SAR';
    }
}
