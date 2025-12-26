<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_cash_handed_over')->default(false)->after('payment_method');
        });

        // Update existing online orders to have is_cash_handed_over = true
        DB::table('orders')->where('payment_method', 'online')->update(['is_cash_handed_over' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_cash_handed_over');
        });
    }
};
