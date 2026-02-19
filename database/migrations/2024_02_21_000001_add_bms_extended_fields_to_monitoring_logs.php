<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            // BMS Extended - Power & Capacity
            $table->decimal('power', 10, 2)->nullable()->after('battery_current')->comment('W, + charge - discharge');
            $table->decimal('remaining_capacity', 10, 3)->nullable()->after('power')->comment('Ah');
            $table->decimal('nominal_capacity', 10, 3)->nullable()->after('remaining_capacity')->comment('Ah');

            // Cycle Count
            $table->unsignedInteger('cycle_count')->nullable()->after('nominal_capacity')->comment('number of cycles');
            $table->decimal('total_cycle_capacity', 12, 3)->nullable()->after('cycle_count')->comment('cumulative Ah');

            // Temperature 2
            $table->decimal('temperature2', 5, 2)->nullable()->after('battery_temperature')->comment('Sensor 2 °C');

            // Balance
            $table->decimal('balance_current', 8, 3)->nullable()->comment('A');
            $table->boolean('is_balancing')->default(false);

            // Alarm & Status
            $table->unsignedInteger('alarm_flags')->nullable()->comment('bit flags');
            $table->string('alarm_text', 255)->nullable();
            $table->tinyInteger('mosfet_status')->nullable()->comment('0=off 1=chg 2=dis 3=both');
            $table->string('mosfet_text', 50)->nullable();

            // Cell diff (mV)
            $table->unsignedInteger('cell_diff_mv')->nullable()->comment('max-min cell voltage diff in mV');

            // Cell resistances JSON
            $table->json('cell_resistances')->nullable()->comment('mΩ per cell');
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropColumn([
                'power', 'remaining_capacity', 'nominal_capacity',
                'cycle_count', 'total_cycle_capacity',
                'temperature2', 'balance_current', 'is_balancing',
                'alarm_flags', 'alarm_text', 'mosfet_status', 'mosfet_text',
                'cell_diff_mv', 'cell_resistances',
            ]);
        });
    }
};
