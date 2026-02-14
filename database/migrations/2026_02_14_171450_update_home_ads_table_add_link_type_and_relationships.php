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
        Schema::table('home_ads', function (Blueprint $table) {
            $table->enum('link_type', ['module', 'product'])->nullable()->after('type');
            $table->foreignId('module_id')->nullable()->after('link_type')->constrained('modules')->nullOnDelete();
        });

        Schema::create('home_ad_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_ad_id')->constrained('home_ads')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_ad_products');

        Schema::table('home_ads', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn(['module_id', 'link_type']);
        });
    }
};
