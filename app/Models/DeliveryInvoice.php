<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'delivery_id',
        'total_amount',
        'status',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Generate a unique invoice number
     * Format: DINV-YYYYMMDD-XXXXX
     */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        
        $lastInvoice = self::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice 
            ? ((int) substr($lastInvoice->invoice_number, -5)) + 1 
            : 1;

        return sprintf('DINV-%s-%05d', $date, $sequence);
    }
}
