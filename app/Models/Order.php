<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Services\LocalizationService;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class Order extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    /**
     * Serialize dates without converting to UTC
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected $fillable = [
        'order_number',
        'user_name',
        'user_phone',
        'user_id',
        'store_id',
        'branch_id',
        'delivery_id',
        'is_delivery',
        'address_id',
        'address_snapshot',
        'payment_method',
        'payment_status',
        'payment_reference',
        'order_status',
        'simple_status',
        'subtotal',
        'delivery_fee',
        'tax',
        'total',
        'notes',
        'order_pickup_image',
        'is_cash_handed_over',
        'is_paid_to_vendor',
        'vendor_invoice_id',
        'delivery_invoice_id',
        'discount',
        'reason',
        'scheduled_time',
    ];

    protected $casts = [
        'address_snapshot' => 'array',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'is_cash_handed_over' => 'boolean',
        'is_paid_to_vendor' => 'boolean',
        'is_delivery' => 'boolean',
        'scheduled_time' => 'string',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['order_status', 'payment_status', 'payment_reference', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Order {$eventName}");
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

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function vendorInvoice()
    {
        return $this->belongsTo(VendorInvoice::class);
    }

    public function deliveryInvoice()
    {
        return $this->belongsTo(DeliveryInvoice::class);
    }

    public function voucherUsage()
    {
        return $this->hasOne(VoucherUsage::class);
    }

    /**
     * Computed attributes
     */
    public function getItemCountAttribute()
    {
        return $this->items->count();
    }

    public function getTotalItemsQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getStatusLabelAttribute()
    {
        return LocalizationService::getMessage("order.status.{$this->order_status}");
    }

    public function getPaymentStatusLabelAttribute()
    {
        return LocalizationService::getMessage("order.payment_status.{$this->payment_status}");
    }

    public function getPaymentMethodLabelAttribute()
    {
        return LocalizationService::getMessage("order.payment_method.{$this->payment_method}");
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Helper methods
     */
    public function canBeCancelled()
    {
        return $this->simple_status === 'in_progress';
    }

    public function isPending()
    {
        return $this->order_status === 'pending';
    }

    public function isPendingPayment()
    {
        return $this->order_status === 'pending_payment';
    }

    public function isConfirmed()
    {
        return $this->order_status === 'confirmed';
    }

    public function isDelivered()
    {
        return $this->order_status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->order_status === 'cancelled';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Simple Status Helper Methods
     */
    public function isInProgress()
    {
        return $this->simple_status === 'in_progress';
    }

    public function isReadyToPick()
    {
        return $this->simple_status === 'ready_to_pick';
    }

    public function isInDelivery()
    {
        return $this->simple_status === 'in_delivery';
    }

    public function isSimpleCancelled()
    {
        return $this->simple_status === 'cancelled';
    }

    public function isSimpleDelivered()
    {
        return $this->simple_status === 'delivered';
    }

    public function getSimpleStatusLabelAttribute()
    {
        return LocalizationService::getMessage("order.simple_status.{$this->simple_status}");
    }
}
