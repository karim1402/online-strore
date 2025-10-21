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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('address_name')->nullable(); // Optional: Custom name for the address
            $table->enum('address_type', ['villa', 'apartment', 'office']); // Type of address
            $table->string('building_name'); // Building name
            $table->string('apartment_number')->nullable(); // Apartment number (nullable for villas)
            $table->string('floor_number')->nullable(); // Floor number
            $table->string('street_name'); // Street name
            $table->string('landmark')->nullable(); // Optional: Nearby landmark (Tombstone)
            $table->string('phone'); // Contact phone for this address
            $table->decimal('latitude', 10, 8)->nullable(); // GPS coordinates
            $table->decimal('longitude', 11, 8)->nullable(); // GPS coordinates
            $table->boolean('is_default')->default(false); // Default shipping address
            $table->timestamps();

            // Index for faster queries
            $table->index('user_id');
            $table->index('is_default');
            $table->index('address_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
