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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('gateway_order_id')->nullable(); // Payment gateway's order ID
            $table->integer('amount_cents');
            $table->string('currency', 10)->default('EGP');
            $table->boolean('success')->default(false);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->boolean('is_3d_secure')->default(false);
            $table->string('card_type')->nullable();
            $table->string('card_pan')->nullable(); // Last 4 digits
            $table->string('gateway_response')->nullable();
            $table->string('txn_response_code')->nullable();
            $table->integer('integration_id')->nullable();
            $table->text('hmac')->nullable();
            $table->decimal('merchant_commission', 10, 2)->default(0.00);
            $table->decimal('accept_fees', 10, 2)->default(0.00);
            $table->timestamp('payment_created_at')->nullable(); // Gateway's timestamp
            $table->json('raw_response')->nullable(); // Store complete gateway response
            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
