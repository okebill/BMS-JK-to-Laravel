<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->default('ESP32-001');
            
            // Protection Settings
            $table->decimal('cell_voltage_overvoltage', 5, 3)->default(3.750)->comment('Cell Voltage Overvoltage (V)');
            $table->decimal('cell_voltage_undervoltage', 5, 3)->default(2.800)->comment('Cell Voltage Undervoltage (V)');
            $table->decimal('cell_voltage_overvoltage_recovery', 5, 3)->default(3.500)->comment('Cell Voltage Overvoltage Recovery (V)');
            $table->decimal('cell_voltage_undervoltage_recovery', 5, 3)->default(2.900)->comment('Cell Voltage Undervoltage Recovery (V)');
            $table->decimal('cell_voltage_balance_start', 5, 3)->default(3.400)->comment('Cell Voltage Balance Start (V)');
            $table->decimal('cell_voltage_balance_delta', 5, 3)->default(0.010)->comment('Cell Voltage Balance Delta (V)');
            
            $table->decimal('total_voltage_overvoltage', 6, 2)->default(60.00)->comment('Total Voltage Overvoltage (V)');
            $table->decimal('total_voltage_undervoltage', 6, 2)->default(44.80)->comment('Total Voltage Undervoltage (V)');
            $table->decimal('total_voltage_overvoltage_recovery', 6, 2)->default(56.00)->comment('Total Voltage Overvoltage Recovery (V)');
            $table->decimal('total_voltage_undervoltage_recovery', 6, 2)->default(46.40)->comment('Total Voltage Undervoltage Recovery (V)');
            
            $table->integer('charge_overcurrent_protection')->default(200)->comment('Charge Overcurrent Protection (A)');
            $table->integer('discharge_overcurrent_protection')->default(200)->comment('Discharge Overcurrent Protection (A)');
            $table->integer('charge_overtemperature_protection')->default(50)->comment('Charge Overtemperature Protection (°C)');
            $table->integer('charge_undertemperature_protection')->default(0)->comment('Charge Undertemperature Protection (°C)');
            $table->integer('discharge_overtemperature_protection')->default(60)->comment('Discharge Overtemperature Protection (°C)');
            $table->integer('discharge_undertemperature_protection')->default(-20)->comment('Discharge Undertemperature Protection (°C)');
            
            // Balance Settings
            $table->integer('balance_start_voltage')->default(3400)->comment('Balance Start Voltage (mV)');
            $table->integer('balance_delta_voltage')->default(10)->comment('Balance Delta Voltage (mV)');
            $table->boolean('balance_enabled')->default(true)->comment('Balance Enabled');
            
            // Device Info
            $table->string('device_name')->nullable()->comment('Device Name');
            $table->string('manufacturing_date')->nullable()->comment('Manufacturing Date');
            $table->string('total_runtime')->nullable()->comment('Total Runtime');
            $table->integer('cycles')->default(0)->comment('Cycles');
            $table->integer('total_charging_time')->default(0)->comment('Total Charging Time (s)');
            $table->integer('total_discharging_time')->default(0)->comment('Total Discharging Time (s)');
            
            // Calibration & Advanced
            $table->decimal('current_calibration', 6, 3)->default(0.000)->comment('Current Calibration');
            $table->integer('sleep_time')->default(0)->comment('Sleep Time (s)');
            $table->string('password')->nullable()->comment('Password');
            $table->boolean('switch_state')->default(true)->comment('Switch State');
            
            // Modbus Register Addresses (untuk write command)
            $table->integer('reg_cell_overvoltage')->default(0x1300)->comment('Register: Cell Overvoltage');
            $table->integer('reg_cell_undervoltage')->default(0x1301)->comment('Register: Cell Undervoltage');
            $table->integer('reg_balance_start')->default(0x1302)->comment('Register: Balance Start');
            $table->integer('reg_balance_delta')->default(0x1303)->comment('Register: Balance Delta');
            
            $table->timestamps();
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bms_settings');
    }
};
