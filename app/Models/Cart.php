<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Cart extends Model
{
    use LogsActivity;

    protected $fillable = [
        'user_id',
        'store_id',
        'campaign_attribution',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'campaign_attribution' => 'array',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'store_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Cart {$eventName}");
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Computed attributes
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->item_total;
        });
    }

    public function getTotalAttribute()
    {
        return $this->subtotal; // In v1, total = subtotal (no tax/delivery)
    }

    public function getItemCountAttribute()
    {
        return $this->items->count();
    }
}
