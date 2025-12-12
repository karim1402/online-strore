<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'addons', 'admins', 'branches', 'carts', 'cart_items', 
            'cart_item_addons', 'cart_item_options', 'categories', 'deliveries', 
            'main_categories', 'option_groups', 'option_values', 'orders', 
            'order_items', 'order_item_addons', 'order_item_options', 'payments', 
            'product_addons', 'product_images', 'product_options', 'product_option_values', 
            'stores', 'users', 'user_addresses', 'vendors'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'addons', 'admins', 'branches', 'carts', 'cart_items', 
            'cart_item_addons', 'cart_item_options', 'categories', 'deliveries', 
            'main_categories', 'option_groups', 'option_values', 'orders', 
            'order_items', 'order_item_addons', 'order_item_options', 'payments', 
            'product_addons', 'product_images', 'product_options', 'product_option_values', 
            'stores', 'users', 'user_addresses', 'vendors'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
