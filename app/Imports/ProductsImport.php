<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductImage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $product = new Product([
            'category_id'     => $row['category_id'] ?? null,
            'subcategory_id'  => $row['subcategory_id'] ?? null,
            'name_en'         => $row['description_en'] ?? $row['name_en'] ?? null, 
            'name_ar'         => $row['description_ar'] ?? $row['name_ar'] ?? null,
            'description_en'  => $row['description_en'] ?? null,
            'description_ar'  => $row['description_ar'] ?? null,
            'quantity_en'     => $row['quantity_en'] ?? null,
            'quantity_ar'     => $row['quantity_ar'] ?? null,
            'base_price'      => $row['base_price'] ,
            'offer_price'     => $row['offer_price'] ?? null,
            'is_active'       => true,
        ]);

        $product->save();

        // Handle image matching by N (number) column
        $number = $row['n'] ?? null;
        $storagePath = 'products/LLlZrhjVe9XKJYitHQ9WHSKPXtoKC9NG8siomwl8.jpg'; // Default fallback image
        
        if ($number) {
            $imagePath = base_path('images/' . $number . '.png');
            
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
}
