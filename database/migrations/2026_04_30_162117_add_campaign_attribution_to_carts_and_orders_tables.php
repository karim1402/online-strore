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
        Schema::table('carts', function (Blueprint $table) {
            $table->json('campaign_attribution')->nullable()->after('store_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->json('campaign_attribution')->nullable()->after('scheduled_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('campaign_attribution');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('campaign_attribution');
        });
    }
};
