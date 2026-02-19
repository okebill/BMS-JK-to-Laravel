<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsSetting extends Model
{
    use HasFactory;

    protected $table = 'bms_settings';

    protected $fillable = [
        'device_id',
        // Protection Settings
        'cell_voltage_overvoltage',
        'cell_voltage_undervoltage',
        'cell_voltage_overvoltage_recovery',
        'cell_voltage_undervoltage_recovery',
        'cell_voltage_balance_start',
        'cell_voltage_balance_delta',
        'total_voltage_overvoltage',
        'total_voltage_undervoltage',
        'total_voltage_overvoltage_recovery',
        'total_voltage_undervoltage_recovery',
        'charge_overcurrent_protection',
        'discharge_overcurrent_protection',
        'charge_overtemperature_protection',
        'charge_undertemperature_protection',
        'discharge_overtemperature_protection',
        'discharge_undertemperature_protection',
        // Balance Settings
        'balance_start_voltage',
        'balance_delta_voltage',
        'balance_enabled',
        // Device Info
        'device_name',
        'manufacturing_date',
        'total_runtime',
        'cycles',
        'total_charging_time',
        'total_discharging_time',
        // Calibration & Advanced
        'current_calibration',
        'sleep_time',
        'password',
        'switch_state',
        // Register addresses
        'reg_cell_overvoltage',
        'reg_cell_undervoltage',
        'reg_balance_start',
        'reg_balance_delta',
    ];

    protected $casts = [
        'balance_enabled' => 'boolean',
        'switch_state' => 'boolean',
    ];

    /**
     * Get or create settings for device
     */
    public static function getForDevice($deviceId = 'ESP32-001')
    {
        return static::firstOrCreate(
            ['device_id' => $deviceId],
            static::getDefaults()
        );
    }

    /**
     * Get default values
     */
    public static function getDefaults()
    {
        return [
            'device_id' => 'ESP32-001',
            'cell_voltage_overvoltage' => 3.750,
            'cell_voltage_undervoltage' => 2.800,
            'cell_voltage_overvoltage_recovery' => 3.500,
            'cell_voltage_undervoltage_recovery' => 2.900,
            'cell_voltage_balance_start' => 3.400,
            'cell_voltage_balance_delta' => 0.010,
            'total_voltage_overvoltage' => 60.00,
            'total_voltage_undervoltage' => 44.80,
            'total_voltage_overvoltage_recovery' => 56.00,
            'total_voltage_undervoltage_recovery' => 46.40,
            'charge_overcurrent_protection' => 200,
            'discharge_overcurrent_protection' => 200,
            'charge_overtemperature_protection' => 50,
            'charge_undertemperature_protection' => 0,
            'discharge_overtemperature_protection' => 60,
            'discharge_undertemperature_protection' => -20,
            'balance_start_voltage' => 3400,
            'balance_delta_voltage' => 10,
            'balance_enabled' => true,
            'switch_state' => true,
        ];
    }
}
