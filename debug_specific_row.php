<?php

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$file = 'c:\Users\Administrator\Desktop\makook\makook\M.P 2 (5).xlsx';

try {
    echo "Reading file... \n";
    $data = Excel::toArray(new ProductsImport, $file);
    $rows = $data[0];

    foreach ($rows as $index => $row) {
        $name = $row['name_en'] ?? $row['description_en'] ?? '';
        
        // Look for the specific failing product: Tresemmé Paraben Shampoo Colorants 400 ml
        if (stripos($name, 'Tresem') !== false) {
            echo "\nFOUND TARGET ROW " . ($index + 2) . ":\n";
            echo "Name: " . $name . "\n";
            
            $rawPrice = $row['base_price'];
            echo "Raw base_price key value: ";
            var_dump($rawPrice);
            
            echo "Sanitization Test:\n";
            $sanitized = $rawPrice;
            if (is_string($sanitized)) {
                $sanitized = str_replace(',', '', $sanitized);
            }
            $float = floatval($sanitized);
            echo "Sanitized float: ";
            var_dump($float);
            
            echo "\nFull Row Data:\n";
            print_r($row);
            break; 
        }
    }

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage();
}
