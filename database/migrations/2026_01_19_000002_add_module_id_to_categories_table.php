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
        Schema::table('categories', function (Blueprint $table) {
            // Add module_id column
            $table->foreignId('module_id')->nullable()->after('id')->constrained('modules')->onDelete('cascade');
            
            // Make store_id nullable (we'll remove it later or keep for backward compatibility)
            $table->foreignId('store_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
            $table->foreignId('store_id')->nullable(false)->change();
        });
    }
};
