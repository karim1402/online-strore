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
        // Add fcm_token to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('remember_token');
        });

        // Add fcm_token to admins table
        Schema::table('admins', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('remember_token');
        });

        // Add fcm_token to vendors table
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('remember_token');
        });

        // Add fcm_token to deliveries table
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('fcm_token')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};
