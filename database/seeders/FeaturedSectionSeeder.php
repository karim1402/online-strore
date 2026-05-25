<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FeaturedSection;
use Illuminate\Database\Seeder;

class FeaturedSectionSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereNull('parent_id')
            ->whereNotIn('module_id', [26, 31, 34])
            ->active()
            ->orderBy('sort_order')
            ->get();

        $sortOrder = 1;

        foreach ($categories as $category) {
            FeaturedSection::firstOrCreate(
                ['type' => 'category', 'item_id' => $category->id],
                ['sort_order' => $sortOrder++, 'is_active' => true]
            );
        }
    }
}
