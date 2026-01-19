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
        if (!Schema::hasColumn('option_values', 'image')) {
            Schema::table('option_values', function (Blueprint $table) {
                $table->string('image')->nullable()->after('value_ar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('option_values', 'image')) {
            Schema::table('option_values', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
