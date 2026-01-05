<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = VendorInvoice::with('store')->latest()->paginate(15);
        return response()->json($invoices);
    }

    /**
     * Mark the invoice as paid.
     */
    public function markAsPaid($id)
    {
        $invoice = VendorInvoice::findOrFail($id);

        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Invoice is already paid.'], 400);
        }

        DB::transaction(function () use ($invoice) {
            // Update invoice status
            $invoice->update(['status' => 'paid']);

            // Update related orders
            $invoice->orders()->update(['is_paid_to_vendor' => true]);
        });

        return response()->json(['message' => 'Invoice marked as paid successfully.', 'invoice' => $invoice]);
    }
}
