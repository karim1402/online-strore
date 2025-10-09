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
        Schema::create('elasticsearch_sync_queue', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            $table->enum('action', ['create', 'update', 'delete']);
            $table->timestamp('synced_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('synced_at');
            $table->index(['entity_type', 'entity_id'], 'idx_entity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elasticsearch_sync_queue');
    }
};
