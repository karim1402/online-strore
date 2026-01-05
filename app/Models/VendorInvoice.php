<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'store_id',
        'total_amount',
        'status',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Generate a unique invoice number
     * Format: INV-YYYYMMDD-XXXXX
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

        return sprintf('INV-%s-%05d', $date, $sequence);
    }
}
