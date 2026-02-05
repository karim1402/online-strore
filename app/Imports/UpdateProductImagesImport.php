<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductImage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UpdateProductImagesImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Get name columns from the row
        $nameEn = $row['name_en'] ?? $row['description_en'] ?? null;
        $nameAr = $row['name_ar'] ?? $row['description_ar'] ?? null;
        
        if (!$nameEn && !$nameAr) {
            Log::warning('UpdateProductImagesImport: No name found in row', $row);
            return null;
        }

        // Find the product by name_en and name_ar
        $query = Product::query();
        
        if ($nameEn) {
            $query->where('name_en', $nameEn);
        }
        if ($nameAr) {
            $query->where('name_ar', $nameAr);
        }
        
        $product = $query->first();
        
        if (!$product) {
            Log::warning('UpdateProductImagesImport: Product not found', [
                'name_en' => $nameEn,
                'name_ar' => $nameAr
            ]);
            return null;
        }

        // Get the image number from the 'n' column
        $number = $row['n'] ?? null;
        
        if (!$number) {
            Log::warning('UpdateProductImagesImport: No image number (n) found for product', [
                'product_id' => $product->id,
                'name_en' => $nameEn
            ]);
            return null;
        }

        // Check if new image exists in "updated images" folder
        $updatedImagesPath = base_path('updated images/' . $number . '.png');
        
        if (!File::exists($updatedImagesPath)) {
            Log::warning('UpdateProductImagesImport: Image not found in updated images folder', [
                'product_id' => $product->id,
                'expected_path' => $updatedImagesPath
            ]);
            return null;
        }

        Log::info('UpdateProductImagesImport: Updating images for product', [
            'product_id' => $product->id,
            'name_en' => $nameEn,
            'image_number' => $number
        ]);

        // Delete existing product images from storage
        foreach ($product->images as $image) {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        // Delete existing product image records
        ProductImage::where('product_id', $product->id)->delete();

        // Copy new image to storage
        $storagePath = 'products/' . $number . '_updated.png';
        Storage::disk('public')->put($storagePath, File::get($updatedImagesPath));

        // Create new ProductImage record
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => $storagePath,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        Log::info('UpdateProductImagesImport: Successfully updated image for product', [
            'product_id' => $product->id,
            'new_image_path' => $storagePath
        ]);

        return null; // Return null since we handle everything manually
    }
}
