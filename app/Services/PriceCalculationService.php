<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductOptionValue;
use App\Models\Addon;

class PriceCalculationService
{
    /**
     * Calculate the final price for a product with selected options and addons
     * 
     * @param Product $product
     * @param array $selectedOptions [product_option_value_id => quantity]
     * @param array $selectedAddons [addon_id => quantity]
     * @param int $quantity
     * @return array
     */
    public static function calculatePrice(Product $product, array $selectedOptions = [], array $selectedAddons = [], int $quantity = 1): array
    {
        $basePrice = (float) $product->base_price;
        $optionPrice = 0;
        $addonPrice = 0;
        $breakdown = [
            'base_price' => $basePrice,
            'options' => [],
            'addons' => [],
            'subtotal' => 0,
            'quantity' => $quantity,
            'total' => 0,
        ];

        // Calculate option prices
        if (!empty($selectedOptions)) {
            foreach ($selectedOptions as $optionValueId) {
                $productOptionValue = ProductOptionValue::with('optionValue')->find($optionValueId);
                
                if ($productOptionValue && $productOptionValue->is_available) {
                    $priceModifier = self::calculateOptionPrice($basePrice, $productOptionValue);
                    $optionPrice += $priceModifier;
                    
                    $breakdown['options'][] = [
                        'id' => $productOptionValue->id,
                        'option_value_id' => $productOptionValue->option_value_id,
                        'name' => $productOptionValue->optionValue->value ?? 'Unknown',
                        'price_type' => $productOptionValue->price_type,
                        'price_value' => (float) $productOptionValue->price_value,
                        'price_modifier' => $priceModifier,
                    ];
                }
            }
        }

        // Calculate addon prices
        if (!empty($selectedAddons)) {
            foreach ($selectedAddons as $addonId) {
                $addon = Addon::find($addonId);
                
                if ($addon && $addon->is_active) {
                    $addonPrice += (float) $addon->price;
                    
                    $breakdown['addons'][] = [
                        'id' => $addon->id,
                        'name' => $addon->name ?? 'Unknown',
                        'price' => (float) $addon->price,
                    ];
                }
            }
        }

        // Calculate final price
        // If there are fixed price options, use the highest one as base
        $hasFixedPrice = false;
        $fixedPrice = $basePrice;
        
        foreach ($breakdown['options'] as $option) {
            if ($option['price_type'] === 'fixed' && $option['price_value'] > $fixedPrice) {
                $fixedPrice = $option['price_value'];
                $hasFixedPrice = true;
            }
        }

        if ($hasFixedPrice) {
            $subtotal = $fixedPrice + $addonPrice;
        } else {
            $subtotal = $basePrice + $optionPrice + $addonPrice;
        }

        $breakdown['subtotal'] = round($subtotal, 2);
        $breakdown['total'] = round($subtotal * $quantity, 2);

        return $breakdown;
    }

    /**
     * Calculate price modifier for a single option
     * 
     * @param float $basePrice
     * @param ProductOptionValue $productOptionValue
     * @return float
     */
    private static function calculateOptionPrice(float $basePrice, ProductOptionValue $productOptionValue): float
    {
        switch ($productOptionValue->price_type) {
            case 'fixed':
                // Fixed price replaces base price
                return (float) $productOptionValue->price_value - $basePrice;
                
            case 'additional':
                // Additional price adds to base price
                return (float) $productOptionValue->price_value;
                
            case 'percentage':
                // Percentage modifier
                return $basePrice * ((float) $productOptionValue->price_value / 100);
                
            default:
                return 0;
        }
    }

    /**
     * Validate selected options for a product
     * 
     * @param Product $product
     * @param array $selectedOptions
     * @return array [valid, errors]
     */
    public static function validateOptions(Product $product, array $selectedOptions): array
    {
        $errors = [];
        $productOptions = $product->productOptions()->with('productOptionValues')->get();

        // Check required options
        foreach ($productOptions as $productOption) {
            if ($productOption->is_required) {
                $hasSelection = false;
                
                foreach ($selectedOptions as $selectedValueId) {
                    $exists = $productOption->productOptionValues()
                        ->where('id', $selectedValueId)
                        ->exists();
                    
                    if ($exists) {
                        $hasSelection = true;
                        break;
                    }
                }

                if (!$hasSelection) {
                    $errors[] = "Option group '{$productOption->optionGroup->name}' is required";
                }
            }
        }

        // Check stock availability
        foreach ($selectedOptions as $selectedValueId) {
            $productOptionValue = ProductOptionValue::find($selectedValueId);
            
            if (!$productOptionValue) {
                $errors[] = "Option value ID {$selectedValueId} not found";
                continue;
            }

            if (!$productOptionValue->is_available) {
                $errors[] = "Option is not available";
            }

            if ($productOptionValue->stock_quantity <= 0) {
                $errors[] = "Option is out of stock";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validate selected addons for a product
     * 
     * @param Product $product
     * @param array $selectedAddons
     * @return array [valid, errors]
     */
    public static function validateAddons(Product $product, array $selectedAddons): array
    {
        $errors = [];
        $productAddons = $product->addons()->pluck('addons.id')->toArray();

        foreach ($selectedAddons as $addonId) {
            if (!in_array($addonId, $productAddons)) {
                $errors[] = "Addon ID {$addonId} is not available for this product";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Check if product with selected options has sufficient stock
     * 
     * @param array $selectedOptions
     * @param int $quantity
     * @return array [available, errors]
     */
    public static function checkStock(array $selectedOptions, int $quantity): array
    {
        $errors = [];

        foreach ($selectedOptions as $selectedValueId) {
            $productOptionValue = ProductOptionValue::find($selectedValueId);
            
            if ($productOptionValue && $productOptionValue->stock_quantity < $quantity) {
                $errors[] = "Insufficient stock. Only {$productOptionValue->stock_quantity} available";
            }
        }

        return [
            'available' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Deduct stock for selected options
     * 
     * @param array $selectedOptions
     * @param int $quantity
     * @return bool
     */
    public static function deductStock(array $selectedOptions, int $quantity): bool
    {
        try {
            foreach ($selectedOptions as $selectedValueId) {
                $productOptionValue = ProductOptionValue::find($selectedValueId);
                
                if ($productOptionValue) {
                    $productOptionValue->decreaseStock($quantity);
                }
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get price range for a product based on all option combinations
     * 
     * @param Product $product
     * @return array [min_price, max_price]
     */
    public static function getPriceRange(Product $product): array
    {
        $basePrice = (float) $product->base_price;
        $minPrice = $basePrice;
        $maxPrice = $basePrice;

        $productOptions = $product->productOptions()->with('productOptionValues')->get();

        foreach ($productOptions as $productOption) {
            foreach ($productOption->productOptionValues as $optionValue) {
                switch ($optionValue->price_type) {
                    case 'fixed':
                        $price = (float) $optionValue->price_value;
                        if ($price < $minPrice) $minPrice = $price;
                        if ($price > $maxPrice) $maxPrice = $price;
                        break;
                        
                    case 'additional':
                        $price = $basePrice + (float) $optionValue->price_value;
                        if ($price < $minPrice) $minPrice = $price;
                        if ($price > $maxPrice) $maxPrice = $price;
                        break;
                        
                    case 'percentage':
                        $price = $basePrice * (1 + (float) $optionValue->price_value / 100);
                        if ($price < $minPrice) $minPrice = $price;
                        if ($price > $maxPrice) $maxPrice = $price;
                        break;
                }
            }
        }

        return [
            'min_price' => round($minPrice, 2),
            'max_price' => round($maxPrice, 2),
        ];
    }
}
