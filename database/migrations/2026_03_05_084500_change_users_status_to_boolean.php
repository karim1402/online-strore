<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert the 'status' column from enum('active','inactive') to boolean.
     */
    public function up(): void
    {
        // First convert existing data: 'active' => 1, 'inactive' => 0
        DB::statement("ALTER TABLE users ADD COLUMN status_bool TINYINT(1) NOT NULL DEFAULT 1 AFTER status");
        DB::statement("UPDATE users SET status_bool = CASE WHEN status = 'active' THEN 1 ELSE 0 END");
        DB::statement("ALTER TABLE users DROP COLUMN status");
        DB::statement("ALTER TABLE users CHANGE COLUMN status_bool status TINYINT(1) NOT NULL DEFAULT 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users ADD COLUMN status_enum ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER status");
        DB::statement("UPDATE users SET status_enum = CASE WHEN status = 1 THEN 'active' ELSE 'inactive' END");
        DB::statement("ALTER TABLE users DROP COLUMN status");
        DB::statement("ALTER TABLE users CHANGE COLUMN status_enum status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    }
};
