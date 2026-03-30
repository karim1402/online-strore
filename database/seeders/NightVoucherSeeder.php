<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class NightVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Voucher::unguard();
        
        // specific ID to bind to our logic
        Voucher::updateOrCreate(
            ['id' => 10000],
            [
                'code' => '9PMOFF',
                'type' => 'fixed',
                'value' => 50.00,
                'min_order_amount' => 300.00,
                'max_discount_amount' => 50.00, // Or set a max discount
                'usage_limit' => 100000,
                'usage_limit_per_user' => 1,
                'is_active' => true,
                'start_date' => now(),
            ]
        );
        
        Voucher::reguard();
    }
}
