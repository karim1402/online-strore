<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItemAddon extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'cart_item_id',
        'addon_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }

    /**
     * Computed attributes
     */
    public function getTotalPriceAttribute()
    {
        return number_format($this->addon->price * $this->quantity, 2, '.', '');
    }
}
