<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\OptionGroup;
use App\Models\OptionValue;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\Addon;
use Illuminate\Support\Facades\DB;

class ProductSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Get first store and category (assuming they exist from previous seeders)
            $store = Store::first();
            $category = Category::first();

            if (!$store || !$category) {
                $this->command->error('Store or Category not found. Please run store and category seeders first.');
                return;
            }

            $this->command->info('Seeding Product System...');

            // 1. Create Option Groups
            $this->command->info('Creating Option Groups...');
            
            $sizeGroup = OptionGroup::create([
                'name_en' => 'Size',
                'name_ar' => 'الحجم',
                'type' => 'size',
                'is_active' => true,
            ]);

            $weightGroup = OptionGroup::create([
                'name_en' => 'Weight',
                'name_ar' => 'الوزن',
                'type' => 'weight',
                'is_active' => true,
            ]);

            $packagingGroup = OptionGroup::create([
                'name_en' => 'Packaging',
                'name_ar' => 'التغليف',
                'type' => 'packaging',
                'is_active' => true,
            ]);

            $crustGroup = OptionGroup::create([
                'name_en' => 'Crust Type',
                'name_ar' => 'نوع العجين',
                'type' => 'crust',
                'is_active' => true,
            ]);

            // 2. Create Option Values
            $this->command->info('Creating Option Values...');

            // Size values
            $sizeSmall = OptionValue::create([
                'option_group_id' => $sizeGroup->id,
                'value_en' => 'Small',
                'value_ar' => 'صغير',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            $sizeMedium = OptionValue::create([
                'option_group_id' => $sizeGroup->id,
                'value_en' => 'Medium',
                'value_ar' => 'متوسط',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            $sizeLarge = OptionValue::create([
                'option_group_id' => $sizeGroup->id,
                'value_en' => 'Large',
                'value_ar' => 'كبير',
                'sort_order' => 3,
                'is_active' => true,
            ]);

            // Weight values
            $weight250 = OptionValue::create([
                'option_group_id' => $weightGroup->id,
                'value_en' => '250g',
                'value_ar' => '250 جم',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            $weight500 = OptionValue::create([
                'option_group_id' => $weightGroup->id,
                'value_en' => '500g',
                'value_ar' => '500 جم',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            $weight1kg = OptionValue::create([
                'option_group_id' => $weightGroup->id,
                'value_en' => '1kg',
                'value_ar' => '1 كجم',
                'sort_order' => 3,
                'is_active' => true,
            ]);

            // Packaging values
            $packagingBox = OptionValue::create([
                'option_group_id' => $packagingGroup->id,
                'value_en' => 'Box',
                'value_ar' => 'صندوق',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            $packagingBag = OptionValue::create([
                'option_group_id' => $packagingGroup->id,
                'value_en' => 'Bag',
                'value_ar' => 'كيس',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            $packagingGlass = OptionValue::create([
                'option_group_id' => $packagingGroup->id,
                'value_en' => 'Glass Jar',
                'value_ar' => 'برطمان زجاجي',
                'sort_order' => 3,
                'is_active' => true,
            ]);

            // Crust values
            $crustThin = OptionValue::create([
                'option_group_id' => $crustGroup->id,
                'value_en' => 'Thin Crust',
                'value_ar' => 'عجين رقيق',
                'sort_order' => 1,
                'is_active' => true,
            ]);

            $crustThick = OptionValue::create([
                'option_group_id' => $crustGroup->id,
                'value_en' => 'Thick Crust',
                'value_ar' => 'عجين سميك',
                'sort_order' => 2,
                'is_active' => true,
            ]);

            // 3. Create Addons
            $this->command->info('Creating Addons...');

            $addonExtraCheese = Addon::create([
                'store_id' => $store->id,
                'name_en' => 'Extra Cheese',
                'name_ar' => 'جبن إضافي',
                'description_en' => 'Add extra cheese to your order',
                'description_ar' => 'أضف جبن إضافي لطلبك',
                'price' => 15.00,
                'addon_category' => 'extras',
                'is_active' => true,
            ]);

            $addonExtraSauce = Addon::create([
                'store_id' => $store->id,
                'name_en' => 'Extra Sauce',
                'name_ar' => 'صلصة إضافية',
                'description_en' => 'Add extra sauce to your order',
                'description_ar' => 'أضف صلصة إضافية لطلبك',
                'price' => 10.00,
                'addon_category' => 'extras',
                'is_active' => true,
            ]);

            $addonSoftDrink = Addon::create([
                'store_id' => $store->id,
                'name_en' => 'Soft Drink',
                'name_ar' => 'مشروب غازي',
                'description_en' => 'Add a soft drink to your meal',
                'description_ar' => 'أضف مشروب غازي لوجبتك',
                'price' => 20.00,
                'addon_category' => 'drinks',
                'is_active' => true,
            ]);

            $addonFries = Addon::create([
                'store_id' => $store->id,
                'name_en' => 'French Fries',
                'name_ar' => 'بطاطس مقلية',
                'description_en' => 'Crispy french fries',
                'description_ar' => 'بطاطس مقلية مقرمشة',
                'price' => 25.00,
                'addon_category' => 'sides',
                'is_active' => true,
            ]);

            // 4. Create Products
            $this->command->info('Creating Products...');

            // Product 1: Pizza (with Size and Crust options)
            $pizza = Product::create([
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name_en' => 'Margherita Pizza',
                'name_ar' => 'بيتزا مارغريتا',
                'description_en' => 'Classic Italian pizza with tomato sauce, mozzarella cheese, and fresh basil',
                'description_ar' => 'بيتزا إيطالية كلاسيكية مع صلصة الطماطم وجبنة الموزاريلا والريحان الطازج',
                'search_keywords' => 'pizza margherita italian food',
                'base_price' => 100.00,
                'is_active' => true,
                'view_count' => 150,
                'sales_count' => 45,
                'sort_order' => 1,
            ]);

            // Assign Size option to Pizza
            $pizzaSizeOption = ProductOption::create([
                'product_id' => $pizza->id,
                'option_group_id' => $sizeGroup->id,
                'is_required' => true,
                'sort_order' => 1,
            ]);

            // Assign size values with pricing
            ProductOptionValue::create([
                'product_option_id' => $pizzaSizeOption->id,
                'option_value_id' => $sizeSmall->id,
                'price_type' => 'additional',
                'price_value' => 0.00,
                'stock_quantity' => 100,
                'is_available' => true,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $pizzaSizeOption->id,
                'option_value_id' => $sizeMedium->id,
                'price_type' => 'additional',
                'price_value' => 30.00,
                'stock_quantity' => 80,
                'is_available' => true,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $pizzaSizeOption->id,
                'option_value_id' => $sizeLarge->id,
                'price_type' => 'additional',
                'price_value' => 50.00,
                'stock_quantity' => 60,
                'is_available' => true,
            ]);

            // Assign Crust option to Pizza
            $pizzaCrustOption = ProductOption::create([
                'product_id' => $pizza->id,
                'option_group_id' => $crustGroup->id,
                'is_required' => false,
                'sort_order' => 2,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $pizzaCrustOption->id,
                'option_value_id' => $crustThin->id,
                'price_type' => 'additional',
                'price_value' => 0.00,
                'stock_quantity' => 200,
                'is_available' => true,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $pizzaCrustOption->id,
                'option_value_id' => $crustThick->id,
                'price_type' => 'additional',
                'price_value' => 15.00,
                'stock_quantity' => 150,
                'is_available' => true,
            ]);

            // Assign addons to Pizza
            $pizza->addons()->attach([
                $addonExtraCheese->id => ['is_available' => true, 'sort_order' => 1],
                $addonExtraSauce->id => ['is_available' => true, 'sort_order' => 2],
                $addonSoftDrink->id => ['is_available' => true, 'sort_order' => 3],
                $addonFries->id => ['is_available' => true, 'sort_order' => 4],
            ]);

            // Product 2: Olive Oil (with Weight and Packaging options)
            $oliveOil = Product::create([
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name_en' => 'Extra Virgin Olive Oil',
                'name_ar' => 'زيت زيتون بكر ممتاز',
                'description_en' => 'Premium quality extra virgin olive oil from Mediterranean',
                'description_ar' => 'زيت زيتون بكر ممتاز عالي الجودة من البحر الأبيض المتوسط',
                'search_keywords' => 'olive oil virgin mediterranean',
                'base_price' => 80.00,
                'is_active' => true,
                'view_count' => 85,
                'sales_count' => 25,
                'sort_order' => 2,
            ]);

            // Assign Weight option to Olive Oil
            $oilWeightOption = ProductOption::create([
                'product_id' => $oliveOil->id,
                'option_group_id' => $weightGroup->id,
                'is_required' => true,
                'sort_order' => 1,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $oilWeightOption->id,
                'option_value_id' => $weight250->id,
                'price_type' => 'additional',
                'price_value' => 0.00,
                'stock_quantity' => 50,
                'is_available' => true,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $oilWeightOption->id,
                'option_value_id' => $weight500->id,
                'price_type' => 'additional',
                'price_value' => 35.00,
                'stock_quantity' => 40,
                'is_available' => true,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $oilWeightOption->id,
                'option_value_id' => $weight1kg->id,
                'price_type' => 'additional',
                'price_value' => 65.00,
                'stock_quantity' => 30,
                'is_available' => true,
            ]);

            // Assign Packaging option to Olive Oil
            $oilPackagingOption = ProductOption::create([
                'product_id' => $oliveOil->id,
                'option_group_id' => $packagingGroup->id,
                'is_required' => false,
                'sort_order' => 2,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $oilPackagingOption->id,
                'option_value_id' => $packagingBag->id,
                'price_type' => 'additional',
                'price_value' => 0.00,
                'stock_quantity' => 100,
                'is_available' => true,
            ]);

            ProductOptionValue::create([
                'product_option_id' => $oilPackagingOption->id,
                'option_value_id' => $packagingGlass->id,
                'price_type' => 'additional',
                'price_value' => 20.00,
                'stock_quantity' => 50,
                'is_available' => true,
            ]);

            // Product 3: Simple Product (no options, with addons)
            $burger = Product::create([
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name_en' => 'Classic Burger',
                'name_ar' => 'برجر كلاسيك',
                'description_en' => 'Juicy beef burger with lettuce, tomato, and special sauce',
                'description_ar' => 'برجر لحم بقري شهي مع الخس والطماطم والصلصة الخاصة',
                'search_keywords' => 'burger beef classic fast food',
                'base_price' => 75.00,
                'is_active' => true,
                'view_count' => 200,
                'sales_count' => 80,
                'sort_order' => 3,
            ]);

            // Assign addons to Burger
            $burger->addons()->attach([
                $addonExtraCheese->id => ['is_available' => true, 'sort_order' => 1],
                $addonSoftDrink->id => ['is_available' => true, 'sort_order' => 2],
                $addonFries->id => ['is_available' => true, 'sort_order' => 3],
            ]);

            $this->command->info('✅ Product System seeded successfully!');
            $this->command->info('');
            $this->command->info('Created:');
            $this->command->info('- 4 Option Groups (Size, Weight, Packaging, Crust)');
            $this->command->info('- 11 Option Values');
            $this->command->info('- 4 Addons');
            $this->command->info('- 3 Products with options and addons');
            $this->command->info('');
            $this->command->info('Sample Products:');
            $this->command->info('1. Margherita Pizza - Base: 100 EGP (with Size & Crust options)');
            $this->command->info('2. Olive Oil - Base: 80 EGP (with Weight & Packaging options)');
            $this->command->info('3. Classic Burger - Fixed: 75 EGP (with addons only)');
        });
    }
}
