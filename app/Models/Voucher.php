<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'start_date',
        'end_date',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'is_active',
        'module_id',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * The module this voucher is restricted to (nullable = global voucher).
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Check if the voucher is valid for the given user, order amount, and cart items.
     *
     * @param  \App\Models\User|null  $user
     * @param  float  $orderAmount
     * @param  \Illuminate\Support\Collection  $cartItems  Cart items with loaded product.category
     */
    public function isValidForUser($user, $orderAmount, $cartItems = null)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return false;
        }

        if ($user) {
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($this->usage_limit_per_user && $userUsageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        // Module restriction check: if this voucher is linked to a module,
        // the cart must contain at least one product from that module.
        if ($this->module_id) {
            if (!$cartItems || $cartItems->isEmpty()) {
                return false;
            }

            $hasModuleProduct = $cartItems->contains(function ($item) {
                return optional($item->product)->module_id == $this->module_id;
            });

            if (!$hasModuleProduct) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the voucher is module-restricted and the given cart items fail that restriction.
     * Used to return a specific error message vs generic invalid.
     */
    public function failsModuleRestriction($cartItems = null): bool
    {
        if (!$this->module_id) {
            return false;
        }

        if (!$cartItems || $cartItems->isEmpty()) {
            return true;
        }

        return !$cartItems->contains(function ($item) {
            return optional($item->product)->module_id == $this->module_id;
        });
    }

    /**
     * Calculate the discount amount for the given order amount.
     */
    public function getDiscountAmount($orderAmount)
    {
        if ($this->type === 'fixed') {
            return min($this->value, $orderAmount);
        } elseif ($this->type === 'percentage') {
            $discount = $orderAmount * ($this->value / 100);
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }
            return min($discount, $orderAmount);
        }

        return 0;
    }

    public function usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }
}
