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
        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();
            
            // Inverter Data
            $table->decimal('pv_voltage', 8, 2)->nullable();
            $table->decimal('pv_current', 8, 2)->nullable();
            $table->decimal('ac_voltage', 8, 2)->nullable();
            $table->decimal('load_power', 10, 2)->nullable();
            
            // BMS Data
            $table->decimal('battery_voltage', 8, 2)->nullable();
            $table->decimal('battery_current', 8, 2)->nullable();
            $table->integer('soc')->nullable()->comment('State of Charge 0-100');
            $table->decimal('battery_temperature', 5, 2)->nullable();
            
            // Cell Voltages (JSON array untuk 16 cells)
            $table->json('cell_voltages')->nullable();
            $table->integer('cell_count')->default(16);
            
            // Metadata
            $table->string('device_id')->nullable()->comment('ESP32 Device ID');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
            
            // Indexes
            $table->index('recorded_at');
            $table->index('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_logs');
    }
};
