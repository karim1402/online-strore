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
        Schema::create('vendor_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_paid_to_vendor')->default(false);
            $table->foreignId('vendor_invoice_id')->nullable()->constrained('vendor_invoices')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['vendor_invoice_id']);
            $table->dropColumn(['vendor_invoice_id', 'is_paid_to_vendor']);
        });

        Schema::dropIfExists('vendor_invoices');
    }
};
