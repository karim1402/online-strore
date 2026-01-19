<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name_en' => 'Electronics',
                'name_ar' => 'الإلكترونيات',
                'description_en' => 'Electronic devices and gadgets',
                'description_ar' => 'الأجهزة الإلكترونية والأدوات',
                'status' => true,
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Clothing',
                'name_ar' => 'الملابس',
                'description_en' => 'Fashion and clothing items',
                'description_ar' => 'الأزياء وعناصر الملابس',
                'status' => true,
                'sort_order' => 2,
            ],
            [
                'name_en' => 'Home & Garden',
                'name_ar' => 'المنزل والحديقة',
                'description_en' => 'Home improvement and garden supplies',
                'description_ar' => 'تحسين المنزل ومستلزمات الحديقة',
                'status' => true,
                'sort_order' => 3,
            ],
            [
                'name_en' => 'Sports & Fitness',
                'name_ar' => 'الرياضة واللياقة البدنية',
                'description_en' => 'Sports equipment and fitness gear',
                'description_ar' => 'المعدات الرياضية ومعدات اللياقة البدنية',
                'status' => true,
                'sort_order' => 4,
            ],
            [
                'name_en' => 'Books & Media',
                'name_ar' => 'الكتب والوسائط',
                'description_en' => 'Books, movies, music and media',
                'description_ar' => 'الكتب والأفلام والموسيقى والوسائط',
                'status' => false,
                'sort_order' => 5,
            ],
            [
                'name_en' => 'Health & Beauty',
                'name_ar' => 'الصحة والجمال',
                'description_en' => 'Health and beauty products',
                'description_ar' => 'منتجات الصحة والجمال',
                'status' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($modules as $module) {
            \App\Models\Module::create($module);
        }
    }
}
