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
        Schema::table('orders', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['user_id']);
            
            // Make user_id nullable
            $table->foreignId('user_id')->nullable()->change();
            
            // Re-add the foreign key with nullable constraint
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['user_id']);
            
            // Make user_id not nullable again
            $table->foreignId('user_id')->nullable(false)->change();
            
            // Re-add the foreign key with restrict on delete
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
        });
    }
};
