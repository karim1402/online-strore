<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Product IDs that block promo code usage and force delivery fee to 10.
     */
    public const RESTRICTED_PRODUCT_IDS = [1045, 1053, 1502, 1503, 1504, 1505, 1506, 1507];

    /**
     * Check if a collection of cart items contains any restricted product.
     *
     * @param  \Illuminate\Support\Collection|null  $cartItems  Cart items with loaded product
     * @return bool
     */
    public static function cartHasRestrictedProducts($cartItems): bool
    {
        if (!$cartItems || $cartItems->isEmpty()) {
            return false;
        }

        return $cartItems->contains(function ($item) {
            return in_array(optional($item->product)->id, self::RESTRICTED_PRODUCT_IDS);
        });
    }

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
        'min_user_orders',
        'min_user_spend',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'min_user_orders' => 'integer',
        'min_user_spend' => 'decimal:2',
    ];

    /**
     * The module this voucher is restricted to (nullable = global voucher).
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Validate the voucher for the given user, order amount, and cart items.
     * Returns true if valid, or a string error key if invalid.
     */
    public function validateForUser($user, $orderAmount, $cartItems = null)
    {
        if (!$this->is_active) {
            return 'errors.voucher_inactive';
        }

        if ($this->start_date && now()->lt($this->start_date)) {
            return 'errors.voucher_not_started';
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return 'errors.voucher_expired';
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return 'errors.voucher_usage_limit_reached';
        }

        if ($this->min_order_amount && $orderAmount < $this->min_order_amount) {
            return 'errors.voucher_min_order_amount';
        }

        if ($user) {
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($this->usage_limit_per_user && $userUsageCount >= $this->usage_limit_per_user) {
                return 'errors.voucher_user_limit_reached';
            }

            // Loyalty Constraints Checks
            // We check past successful orders: simple_status == 'delivered' OR simple_status == 'completed'
            // and payment_status == 'paid' (or cash orders that are delivered).
            // A common metric implies just checking 'completed' or 'delivered' orders.
            $successfulOrdersQuery = $user->orders()->whereIn('simple_status', ['delivered', 'completed']);

            if ($this->min_user_orders) {
                $pastOrdersCount = (clone $successfulOrdersQuery)->count();
                if ($pastOrdersCount < $this->min_user_orders) {
                    return 'errors.voucher_min_orders_required';
                }
            }

            if ($this->min_user_spend) {
                $pastTotalSpend = (clone $successfulOrdersQuery)->sum('total');
                if ($pastTotalSpend < $this->min_user_spend) {
                    return 'errors.voucher_min_spend_required';
                }
            }
        }

        if ($this->failsModuleRestriction($cartItems)) {
            return 'errors.voucher_module_restricted';
        }

        // Block voucher if cart contains restricted products
        if ($cartItems && self::cartHasRestrictedProducts($cartItems)) {
            return 'errors.voucher_restricted_products';
        }

        // Night Voucher Validation (Only valid from 9:00 PM to 9:00 AM)
        if ($this->id == 10000) {
            $hour = now()->format('H');
            if ($hour >= 3 && $hour < 21) {
                return 'errors.voucher_invalid_time';
            }
        }

        return true;
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
        return $this->validateForUser($user, $orderAmount, $cartItems) === true;
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
