<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateDeliveredOrdersPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-delivered-payment-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the payment status to paid for all delivered orders.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updatedCount = \App\Models\Order::where('simple_status', 'delivered')
            ->where('payment_status', '!=', 'paid')
            ->update(['payment_status' => 'paid']);

        $this->info("Successfully updated {$updatedCount} delivered orders to 'paid' payment status.");
    }
}
