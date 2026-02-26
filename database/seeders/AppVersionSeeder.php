<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppSetting;

class AppVersionSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'android' => [
                'minimum_version'         => '1.5.5',
                'latest_version'          => '1.5.6',
                'force_update_message'    => 'A critical update is required to continue.',
                'optional_update_message' => 'A new version 1.5.6 is available with improvements!',
            ],
            'ios' => [
                'minimum_version'         => '1.5.5',
                'latest_version'          => '1.5.6',
                'force_update_message'    => 'A critical update is required to continue.',
                'optional_update_message' => 'A new version 1.5.6 is available with improvements!',
            ],
        ];

        foreach ($defaults as $platform => $data) {
            AppSetting::setAppVersion($platform, $data);
        }
    }
}
