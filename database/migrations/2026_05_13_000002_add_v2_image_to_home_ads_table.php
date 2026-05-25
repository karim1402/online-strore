<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_ads', function (Blueprint $table) {
            $table->string('image_v2')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('home_ads', function (Blueprint $table) {
            $table->dropColumn('image_v2');
        });
    }
};
