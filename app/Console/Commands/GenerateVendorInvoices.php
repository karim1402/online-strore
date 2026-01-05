<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\VendorInvoice;
use Illuminate\Support\Facades\DB;

class GenerateVendorInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-vendor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for vendors for unpaid online orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting vendor invoice generation...');

        // 1. Find all eligible orders: Online, Delivered, Not Paid to Vendor, Not assigned to an invoice
        $orders = Order::where('payment_method', 'online')
            ->where('simple_status', 'delivered')
            ->where('is_paid_to_vendor', false)
            ->whereNull('vendor_invoice_id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No eligible orders found.');
            return;
        }

        // 2. Group by Store
        $ordersByStore = $orders->groupBy('store_id');

        $this->info("Found " . $orders->count() . " orders across " . $ordersByStore->count() . " stores.");

        foreach ($ordersByStore as $storeId => $storeOrders) {
            DB::transaction(function () use ($storeId, $storeOrders) {
                // Calculate total amount for the invoice
                // Assuming we pay the full 'total' of the order to the vendor. 
                // Adjust this if commission needs to be deducted.
                $totalAmount = $storeOrders->sum('total');

                // Create Invoice
                $invoice = VendorInvoice::create([
                    'invoice_number' => VendorInvoice::generateInvoiceNumber(),
                    'store_id' => $storeId,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                // Update Orders
                foreach ($storeOrders as $order) {
                    $order->update([
                        'vendor_invoice_id' => $invoice->id,
                    ]);
                }

                $this->info("Created Invoice #{$invoice->id} for Store #{$storeId} with amount {$totalAmount}");
            });
        }

        $this->info('Vendor invoice generation completed.');
    }
}
