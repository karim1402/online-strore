<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\DeliveryInvoice;
use Illuminate\Support\Facades\DB;

class GenerateDeliveryInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-delivery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoices for delivery drivers for cash orders not yet handed over';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting delivery invoice generation...');

        // Find all eligible orders:
        // - Cash payment method
        // - Delivered
        // - Cash not handed over yet (is_cash_handed_over = false)
        // - Has a delivery assigned
        // - Not already assigned to a delivery invoice
        $orders = Order::where('payment_method', 'cash')
            ->where('simple_status', 'delivered')
            ->where('is_cash_handed_over', false)
            ->whereNotNull('delivery_id')
            ->whereNull('delivery_invoice_id')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No eligible orders found.');
            return;
        }

        // Group by Delivery
        $ordersByDelivery = $orders->groupBy('delivery_id');

        $this->info("Found " . $orders->count() . " orders across " . $ordersByDelivery->count() . " delivery drivers.");

        foreach ($ordersByDelivery as $deliveryId => $deliveryOrders) {
            DB::transaction(function () use ($deliveryId, $deliveryOrders) {
                // Calculate total amount for the invoice
                // This is the total cash the delivery driver needs to hand over
                $totalAmount = $deliveryOrders->sum('total');

                // Create Invoice
                $invoice = DeliveryInvoice::create([
                    'invoice_number' => DeliveryInvoice::generateInvoiceNumber(),
                    'delivery_id' => $deliveryId,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                ]);

                // Update Orders
                foreach ($deliveryOrders as $order) {
                    $order->update([
                        'delivery_invoice_id' => $invoice->id,
                    ]);
                }

                $this->info("Created Invoice #{$invoice->invoice_number} for Delivery #{$deliveryId} with amount {$totalAmount}");
            });
        }

        $this->info('Delivery invoice generation completed.');
    }
}
