<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddDefaultImageToProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $imagePath = 'products/LLlZrhjVe9XKJYitHQ9WHSKPXtoKC9NG8siomwl8.jpg';

        $products = Product::all();

        foreach ($products as $product) {
            // Check if this image already exists for the product
            $exists = ProductImage::where('product_id', $product->id)
                ->where('image_path', $imagePath)
                ->exists();

            if (!$exists) {
                // Get current max sort order
                $maxSortOrder = ProductImage::where('product_id', $product->id)->max('sort_order') ?? -1;

                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'is_primary' => false, // Default to not primary
                    'sort_order' => $maxSortOrder + 1,
                ]);
            }
        }
    }
}
