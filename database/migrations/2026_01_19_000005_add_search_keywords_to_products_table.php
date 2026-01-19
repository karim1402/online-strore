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
        if (!Schema::hasColumn('products', 'search_keywords')) {
            Schema::table('products', function (Blueprint $table) {
                $table->text('search_keywords')->nullable()->comment('For Elasticsearch optimization')->after('description_ar');
                $table->fullText('search_keywords');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'search_keywords')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropFullText(['search_keywords']);
                $table->dropColumn('search_keywords');
            });
        }
    }
};
