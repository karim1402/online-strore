<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItemOption extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'cart_item_id',
        'product_option_value_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function productOptionValue()
    {
        return $this->belongsTo(ProductOptionValue::class);
    }

    public function optionValue()
    {
        return $this->hasOneThrough(
            OptionValue::class,
            ProductOptionValue::class,
            'id', // Foreign key on ProductOptionValue
            'id', // Foreign key on OptionValue
            'product_option_value_id', // Local key on CartItemOption
            'option_value_id' // Local key on ProductOptionValue
        );
    }
}
