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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('name_en');
            $table->string('name_ar');
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('search_keywords')->nullable()->comment('For Elasticsearch optimization');
            $table->decimal('base_price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->integer('view_count')->default(0)->comment('For popularity ranking');
            $table->integer('sales_count')->default(0)->comment('For popularity ranking');
            $table->json('metadata')->nullable()->comment('For flexible future data');
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('store_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('view_count');
            $table->index('sales_count');
            $table->index('deleted_at');
            $table->fullText('search_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
