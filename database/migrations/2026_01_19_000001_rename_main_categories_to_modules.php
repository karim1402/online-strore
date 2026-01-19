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
        Schema::rename('main_categories', 'modules');
        if (Schema::hasTable('main_category_store')) {
            Schema::rename('main_category_store', 'module_store');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('module_store')) {
            Schema::rename('module_store', 'main_category_store');
        }
        Schema::rename('modules', 'main_categories');
    }
};
