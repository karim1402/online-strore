<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupCartItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove cart items that reference deleted or unavailable products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cart cleanup...');

        // Get all product IDs that are available (not soft-deleted)
        $availableProductIds = Product::pluck('id')->toArray();

        // Find cart items whose product is deleted or doesn't exist
        $invalidCartItems = CartItem::whereNotIn('product_id', $availableProductIds)->get();

        if ($invalidCartItems->isEmpty()) {
            $this->info('All cart items are valid. No cleanup needed.');
            return 0;
        }

        $this->info("Found {$invalidCartItems->count()} cart item(s) with deleted/unavailable products.");

        DB::beginTransaction();

        try {
            $removedCount = 0;

            foreach ($invalidCartItems as $cartItem) {
                // Delete related options and addons first
                $cartItem->options()->delete();
                $cartItem->addons()->delete();
                $cartItem->delete();
                $removedCount++;
            }

            DB::commit();

            $this->info("Successfully removed {$removedCount} cart item(s) with deleted products.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to cleanup cart items: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
