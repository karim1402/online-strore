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
        Schema::create('cart_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained('cart_items')->onDelete('cascade');
            $table->foreignId('product_option_value_id')->constrained('product_option_values')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('cart_item_id');
            $table->index('product_option_value_id');
            
            // Unique constraint to prevent duplicate options per cart item
            $table->unique(['cart_item_id', 'product_option_value_id'], 'cart_item_option_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_options');
    }
};
