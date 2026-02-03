<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'category_id'     => $row['category_id'] ?? null,
            'subcategory_id'  => $row['subcategory_id'] ?? null,
            'name_en'         => $row['description_en'] ?? $row['name_en'] ?? null, 
            'name_ar'         => $row['description_ar'] ?? $row['name_ar'] ?? null,
            'description_en'  => $row['description_en'] ?? null,
            'description_ar'  => $row['description_ar'] ?? null,
            'quantity_en'     => $row['quantity_en'] ?? null,
            'quantity_ar'     => $row['quantity_ar'] ?? null,
            'base_price'      => $row['base_price'] ?? 0,
            'offer_price'     => $row['offer_price'] ?? null,
            'is_active'       => true,
            // 'store_id' => 1, // Default store ID? Validation usually requires it.
        ]);
    }
}
