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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('level', 20)->default('info')->comment('info, error, warning, debug');
            $table->string('device_id')->nullable()->comment('ESP32 Device ID');
            $table->timestamps();
            
            // Indexes
            $table->index('created_at');
            $table->index('level');
            $table->index('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
