<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryInvoiceController extends Controller
{
    /**
     * List all delivery invoices
     */
    public function index(Request $request)
    {
        $query = DeliveryInvoice::with('delivery')
            ->withCount('orders')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by delivery
        if ($request->filled('delivery_id')) {
            $query->where('delivery_id', $request->delivery_id);
        }

        return $query->paginate(15);
    }

    /**
     * Get a specific delivery invoice with orders
     */
    public function show($id)
    {
        $invoice = DeliveryInvoice::with(['delivery', 'orders' => function ($query) {
            $query->with(['user', 'store', 'items'])->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $invoice
        ]);
    }

    /**
     * Mark a delivery invoice as paid (cash handed over)
     */
    public function markAsPaid($id)
    {
        $invoice = DeliveryInvoice::findOrFail($id);

        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'Invoice is already paid.'
            ], 400);
        }

        DB::transaction(function () use ($invoice) {
            // Update invoice status
            $invoice->update(['status' => 'paid']);

            // Update related orders - mark cash as handed over
            $invoice->orders()->update(['is_cash_handed_over' => true]);
        });

        return response()->json([
            'message' => 'Invoice marked as paid successfully.',
            'invoice' => $invoice->fresh()
        ]);
    }
}
