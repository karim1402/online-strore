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
        Schema::create('cart_item_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained('cart_items')->onDelete('cascade');
            $table->foreignId('addon_id')->constrained('addons')->onDelete('cascade');
            $table->integer('quantity')->unsigned()->default(1);
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('cart_item_id');
            $table->index('addon_id');
            
            // Unique constraint to prevent duplicate addons per cart item
            $table->unique(['cart_item_id', 'addon_id'], 'cart_item_addon_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item_addons');
    }
};
