<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Storage;

class CheckMissingProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $missing = [];

        foreach ($rows as $row) {
            // Determine the name exactly as ProductsImport does
            $name =  $row['description_en'] ;

            if (!$name) {
                continue;
            }

            // Check if product exists in DB by English Name (most reliable unique identifier here)
            // matching the logic of what we consider 'Existing'
            $exists = Product::where('name_en', $name)
                            //  ->orWhere('description_en', $name)
                             ->exists();

            if (!$exists) {
                // If it doesn't exist, we add it to our list
                $missing[] = $name;
            }
        }

        // Save to text file
        $content = implode("\n", $missing);
        Storage::disk('local')->put('missing_products.txt', $content);
    }
}
