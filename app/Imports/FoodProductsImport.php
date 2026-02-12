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

class FoodProductsImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            // Sanitize base price
            $basePrice = $row['base_price'] ?? 0;
            if (is_string($basePrice)) {
                $basePrice = str_replace(',', '', $basePrice);
            }
            $basePrice = floatval($basePrice);

            // Sanitize offer price
            $offerPrice = $row['offer_price'] ?? null;
            if ($offerPrice !== null && $offerPrice !== '') {
                $offerPrice = str_replace(',', '', (string)$offerPrice);
                if (!is_numeric($offerPrice)) {
                    $offerPrice = null;
                } else {
                    $offerPrice = floatval($offerPrice);
                }
            } else {
                $offerPrice = null;
            }

            $product = new Product([
                'category_id'     => $row['category_id'] ?? null,
                'name_en'         => $row['name_en'] ?? null, 
                'name_ar'         => $row['name_ar'] ?? null,
                'description_en'  => $row['description_en'] ?? null,
                'description_ar'  => $row['description_ar'] ?? null,
                'quantity_en'     => $row['quantity_en'] ?? null,
                'quantity_ar'     => $row['quantity_ar'] ?? null,
                'offer_price'     => $offerPrice,
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

        } catch (\Throwable $e) {
            Log::error('Food Product Import Failed at Row: ' . json_encode($row) . ' Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
