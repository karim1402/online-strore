<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->index();
            $table->text('token');
            $table->nullableMorphs('tokenable'); // tokenable_id + tokenable_type
            $table->string('platform')->nullable(); // ios / android / web
            $table->timestamps();

            $table->unique(['device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fcm_tokens');
    }
};
