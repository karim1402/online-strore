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
        Schema::create('main_category_store', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('main_category_id')->constrained('main_categories')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['store_id', 'main_category_id']);
            
            // Indexes for better query performance
            $table->index('store_id');
            $table->index('main_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_category_store');
    }
};
