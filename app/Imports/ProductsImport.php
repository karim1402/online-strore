<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductImage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Concerns\SkipsOnError;

class ProductsImport implements ToModel, WithHeadingRow, WithCalculatedFormulas, SkipsOnError
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Sanitize base price
        $basePrice = $row['base_price'];
        // Remove commas if present and ensure it's a valid number format
        if (is_string($basePrice)) {
            $basePrice = str_replace(',', '', $basePrice);
        }
        $basePrice = floatval($basePrice);

        Log::info('Importing Product Row:', [
            'name' => $row['name_en'] ?? $row['description_en'],
            'raw_price' => $row['base_price'],
            'sanitized_price' => $basePrice
        ]);



        $product = new Product([
            'category_id'     => $row['category_id'] ?? null,
            'subcategory_id'  => $row['subcategory_id'] ?? null,
            'name_en'         => $row['description_en'] ,
            'name_ar'         => $row['description_ar'] ,
            'description_en'  => $row['description_en'] ,
            'description_ar'  => $row['description_ar'] ,
            'quantity_en'     => $row['quantity_en'] ?? null,
            'quantity_ar'     => $row['quantity_ar'] ?? null,
            // 'base_price'      => $basePrice, // Moved to explicit assignment below
            'offer_price'     => $row['offer_price'] ?? null,
            'is_active'       => true,
        ]);

        $product->base_price = $basePrice;
        $product->save();

        // Handle image matching by N (number) column
        $number = $row['n'] ?? null;
        $storagePath = 'products/LLlZrhjVe9XKJYitHQ9WHSKPXtoKC9NG8siomwl8.jpg'; // Default fallback image
        
        if ($number) {
            $imagePath = base_path('images2/' . $number . '.png');
            
            if (File::exists($imagePath)) {
                // Copy image to storage
                $storagePath = 'products/' . $number . '.png';
                Storage::disk('public')->put($storagePath, File::get($imagePath));
            }
        }
        
        // Create ProductImage record (uses matched image or fallback)
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $storagePath,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        return null; // Return null since we already saved the product
    }
    public function onError(\Throwable $e)
    {
        Log::error('Product Import Error: ' . $e->getMessage());
    }
}
