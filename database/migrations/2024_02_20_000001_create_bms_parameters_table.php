<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->default('ESP32-001');
            
            // Voltage Settings (dari register 0x1000+)
            $table->decimal('smart_sleep', 8, 3)->nullable()->comment('Smart Sleep Voltage (V)');
            $table->decimal('cell_uvp', 8, 3)->nullable()->comment('Cell Undervoltage Protection (V)');
            $table->decimal('cell_uvpr', 8, 3)->nullable()->comment('Cell Undervoltage Protection Recovery (V)');
            $table->decimal('cell_ovp', 8, 3)->nullable()->comment('Cell Overvoltage Protection (V)');
            $table->decimal('cell_ovpr', 8, 3)->nullable()->comment('Cell Overvoltage Protection Recovery (V)');
            $table->decimal('balance_trigger', 8, 3)->nullable()->comment('Balance Trigger Voltage (V)');
            $table->decimal('soc_100', 8, 3)->nullable()->comment('SOC 100% Voltage (V)');
            $table->decimal('soc_0', 8, 3)->nullable()->comment('SOC 0% Voltage (V)');
            $table->decimal('cell_rcv', 8, 3)->nullable()->comment('Cell RCV Voltage (V)');
            $table->decimal('cell_rfv', 8, 3)->nullable()->comment('Cell RFV Voltage (V)');
            $table->decimal('system_power_off', 8, 3)->nullable()->comment('System Power Off Voltage (V)');
            
            // Current Settings
            $table->decimal('charge_coc', 8, 3)->nullable()->comment('Charge Continued Overcurrent (A)');
            $table->decimal('discharge_coc', 8, 3)->nullable()->comment('Discharge Continued Overcurrent (A)');
            $table->decimal('max_balance_current', 8, 3)->nullable()->comment('Max Balance Current (A)');
            
            // Temperature Settings
            $table->decimal('charge_otp', 6, 1)->nullable()->comment('Charge Overtemperature Protection (°C)');
            $table->decimal('charge_otpr', 6, 1)->nullable()->comment('Charge OTP Recovery (°C)');
            $table->decimal('discharge_otp', 6, 1)->nullable()->comment('Discharge Overtemperature Protection (°C)');
            $table->decimal('discharge_otpr', 6, 1)->nullable()->comment('Discharge OTP Recovery (°C)');
            $table->decimal('charge_utp', 6, 1)->nullable()->comment('Charge Undertemperature Protection (°C)');
            $table->decimal('charge_utpr', 6, 1)->nullable()->comment('Charge UTP Recovery (°C)');
            $table->decimal('mos_otp', 6, 1)->nullable()->comment('MOS Overtemperature Protection (°C)');
            $table->decimal('mos_otpr', 6, 1)->nullable()->comment('MOS OTP Recovery (°C)');
            
            // Battery Info
            $table->integer('cell_count')->nullable()->comment('Cell Count');
            $table->decimal('battery_capacity', 8, 2)->nullable()->comment('Battery Capacity (Ah)');
            $table->decimal('balance_start_voltage', 8, 3)->nullable()->comment('Balance Start Voltage (V)');
            
            $table->timestamps();
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_parameters');
    }
};
