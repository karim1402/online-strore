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
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_option_id')->constrained('product_options')->onDelete('cascade');
            $table->foreignId('option_value_id')->constrained('option_values')->onDelete('cascade');
            $table->enum('price_type', ['fixed', 'additional', 'percentage'])->default('additional');
            $table->decimal('price_value', 10, 2)->default(0.00);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Unique constraint to prevent duplicate value assignments
            $table->unique(['product_option_id', 'option_value_id'], 'unique_product_option_value');
            
            // Indexes
            $table->index('product_option_id');
            $table->index('option_value_id');
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};
