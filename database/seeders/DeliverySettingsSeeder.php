<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class DeliverySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default delivery fees
        AppSetting::set('delivery_base_fee', '10.00'); // Example base fee
        AppSetting::set('delivery_km_fee', '5.00');   // Example per km fee
        
        // Service center coordinates (using the same as AddressController)
        AppSetting::set('delivery_start_lat', '30.7989597');
        AppSetting::set('delivery_start_lng', '31.0065842');
        
    }
}
