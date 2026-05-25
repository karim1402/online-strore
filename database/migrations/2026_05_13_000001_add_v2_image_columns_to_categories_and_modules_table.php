<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image_v2')->nullable()->after('image');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->string('image_v2')->nullable()->after('image');
            $table->string('image_ar_v2')->nullable()->after('image_ar');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image_v2');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['image_v2', 'image_ar_v2']);
        });
    }
};
