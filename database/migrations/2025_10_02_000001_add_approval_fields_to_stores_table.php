<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            // Change status from boolean to enum
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])
                ->default('pending')
                ->change();
            
            // Add rejection/suspension note
            $table->text('rejection_note')->nullable()->after('status');
            
            // Add approval tracking
            $table->timestamp('approved_at')->nullable()->after('rejection_note');
            $table->foreignId('approved_by')->nullable()->after('approved_at')
                ->constrained('admins')->nullOnDelete();
        });
        
        // Update existing records: false -> pending, true -> approved
        DB::statement("UPDATE stores SET status = CASE WHEN status = '1' THEN 'approved' ELSE 'pending' END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['rejection_note', 'approved_at', 'approved_by']);
            
            // Revert status to boolean
            $table->boolean('status')->default(false)->change();
        });
    }
};
