<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OptionGroup;
use App\Models\OptionValue;
use Illuminate\Support\Facades\DB;

class OptionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates basic option groups: Size, Weight, and Volume (Liter)
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // 1. Size Option Group
            $sizeGroup = OptionGroup::create([
                'name_en' => 'Size',
                'name_ar' => 'الحجم',
                'type' => 'size',
                'is_active' => true,
            ]);

            $sizeValues = [
                ['value_en' => 'Small', 'value_ar' => 'صغير', 'sort_order' => 1],
                ['value_en' => 'Medium', 'value_ar' => 'وسط', 'sort_order' => 2],
                ['value_en' => 'Large', 'value_ar' => 'كبير', 'sort_order' => 3],
                ['value_en' => 'Extra Large', 'value_ar' => 'كبير جداً', 'sort_order' => 4],
            ];

            foreach ($sizeValues as $value) {
                OptionValue::create(array_merge($value, [
                    'option_group_id' => $sizeGroup->id,
                    'is_active' => true,
                ]));
            }

            // 2. Weight Option Group
            $weightGroup = OptionGroup::create([
                'name_en' => 'Weight',
                'name_ar' => 'الوزن',
                'type' => 'weight',
                'is_active' => true,
            ]);

            $weightValues = [
                ['value_en' => '100g', 'value_ar' => '100 جرام', 'sort_order' => 1],
                ['value_en' => '250g', 'value_ar' => '250 جرام', 'sort_order' => 2],
                ['value_en' => '500g', 'value_ar' => '500 جرام', 'sort_order' => 3],
                ['value_en' => '1kg', 'value_ar' => '1 كيلوجرام', 'sort_order' => 4],
                ['value_en' => '2kg', 'value_ar' => '2 كيلوجرام', 'sort_order' => 5],
                ['value_en' => '5kg', 'value_ar' => '5 كيلوجرام', 'sort_order' => 6],
            ];

            foreach ($weightValues as $value) {
                OptionValue::create(array_merge($value, [
                    'option_group_id' => $weightGroup->id,
                    'is_active' => true,
                ]));
            }

            // 3. Volume (Liter) Option Group
            $volumeGroup = OptionGroup::create([
                'name_en' => 'Volume',
                'name_ar' => 'الحجم (لتر)',
                'type' => 'volume',
                'is_active' => true,
            ]);

            $volumeValues = [
                ['value_en' => '250ml', 'value_ar' => '250 مل', 'sort_order' => 1],
                ['value_en' => '500ml', 'value_ar' => '500 مل', 'sort_order' => 2],
                ['value_en' => '750ml', 'value_ar' => '750 مل', 'sort_order' => 3],
                ['value_en' => '1L', 'value_ar' => '1 لتر', 'sort_order' => 4],
                ['value_en' => '1.5L', 'value_ar' => '1.5 لتر', 'sort_order' => 5],
                ['value_en' => '2L', 'value_ar' => '2 لتر', 'sort_order' => 6],
                ['value_en' => '3L', 'value_ar' => '3 لتر', 'sort_order' => 7],
                ['value_en' => '5L', 'value_ar' => '5 لتر', 'sort_order' => 8],
            ];

            foreach ($volumeValues as $value) {
                OptionValue::create(array_merge($value, [
                    'option_group_id' => $volumeGroup->id,
                    'is_active' => true,
                ]));
            }

            DB::commit();

            $this->command->info('✅ Option groups and values seeded successfully!');
            $this->command->info('📊 Created 3 option groups with their values:');
            $this->command->info('   1. Size (4 values: Small, Medium, Large, Extra Large)');
            $this->command->info('   2. Weight (6 values: 100g, 250g, 500g, 1kg, 2kg, 5kg)');
            $this->command->info('   3. Volume (8 values: 250ml, 500ml, 750ml, 1L, 1.5L, 2L, 3L, 5L)');
            $this->command->info('📝 Total: 18 option values created');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error seeding option groups: ' . $e->getMessage());
            throw $e;
        }
    }
}
