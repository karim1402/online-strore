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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->string('quantity_en')->nullable()->after('sales_count');
            $table->string('quantity_ar')->nullable()->after('quantity_en');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['quantity_en', 'quantity_ar']);
            $table->string('quantity')->nullable()->after('sales_count');
        });
    }
};
