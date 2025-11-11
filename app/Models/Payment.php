<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'gateway_order_id',
        'amount_cents',
        'currency',
        'success',
        'status',
        'is_3d_secure',
        'card_type',
        'card_pan',
        'gateway_response',
        'txn_response_code',
        'integration_id',
        'hmac',
        'merchant_commission',
        'accept_fees',
        'payment_created_at',
        'raw_response',
    ];

    protected $casts = [
        'success' => 'boolean',
        'is_3d_secure' => 'boolean',
        'amount_cents' => 'integer',
        'integration_id' => 'integer',
        'merchant_commission' => 'decimal:2',
        'accept_fees' => 'decimal:2',
        'raw_response' => 'array',
        'payment_created_at' => 'datetime',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'success', 'transaction_id', 'amount_cents'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Payment {$eventName}");
    }

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scopes
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true)->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('success', false)->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Helper methods
     */
    public function isSuccessful()
    {
        return $this->success && $this->status === 'completed';
    }

    public function isFailed()
    {
        return !$this->success && $this->status === 'failed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function getAmountAttribute()
    {
        return $this->amount_cents / 100;
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }
}
