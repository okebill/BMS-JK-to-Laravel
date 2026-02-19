<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsParameter extends Model
{
    use HasFactory;

    protected $table = 'bms_parameters';

    protected $fillable = [
        'device_id',
        'smart_sleep',
        'cell_uvp',
        'cell_uvpr',
        'cell_ovp',
        'cell_ovpr',
        'balance_trigger',
        'soc_100',
        'soc_0',
        'cell_rcv',
        'cell_rfv',
        'system_power_off',
        'charge_coc',
        'discharge_coc',
        'max_balance_current',
        'charge_otp',
        'charge_otpr',
        'discharge_otp',
        'discharge_otpr',
        'charge_utp',
        'charge_utpr',
        'mos_otp',
        'mos_otpr',
        'cell_count',
        'battery_capacity',
        'balance_start_voltage',
    ];

    protected $casts = [
        'smart_sleep' => 'decimal:3',
        'cell_uvp' => 'decimal:3',
        'cell_uvpr' => 'decimal:3',
        'cell_ovp' => 'decimal:3',
        'cell_ovpr' => 'decimal:3',
        'balance_trigger' => 'decimal:3',
        'soc_100' => 'decimal:3',
        'soc_0' => 'decimal:3',
        'cell_rcv' => 'decimal:3',
        'cell_rfv' => 'decimal:3',
        'system_power_off' => 'decimal:3',
        'charge_coc' => 'decimal:3',
        'discharge_coc' => 'decimal:3',
        'max_balance_current' => 'decimal:3',
        'charge_otp' => 'decimal:1',
        'charge_otpr' => 'decimal:1',
        'discharge_otp' => 'decimal:1',
        'discharge_otpr' => 'decimal:1',
        'charge_utp' => 'decimal:1',
        'charge_utpr' => 'decimal:1',
        'mos_otp' => 'decimal:1',
        'mos_otpr' => 'decimal:1',
        'battery_capacity' => 'decimal:2',
        'balance_start_voltage' => 'decimal:3',
    ];

    /**
     * Get or create parameters for device
     */
    public static function getForDevice($deviceId = 'ESP32-001')
    {
        return static::firstOrCreate(
            ['device_id' => $deviceId]
        );
    }

    /**
     * Update parameters from BMS settings JSON
     */
    public function updateFromSettings($settings)
    {
        $updateData = [];
        
        if (isset($settings['smart_sleep'])) $updateData['smart_sleep'] = $settings['smart_sleep'];
        if (isset($settings['cell_uvp'])) $updateData['cell_uvp'] = $settings['cell_uvp'];
        if (isset($settings['cell_uvpr'])) $updateData['cell_uvpr'] = $settings['cell_uvpr'];
        if (isset($settings['cell_ovp'])) $updateData['cell_ovp'] = $settings['cell_ovp'];
        if (isset($settings['cell_ovpr'])) $updateData['cell_ovpr'] = $settings['cell_ovpr'];
        if (isset($settings['balance_trigger'])) $updateData['balance_trigger'] = $settings['balance_trigger'];
        if (isset($settings['soc_100'])) $updateData['soc_100'] = $settings['soc_100'];
        if (isset($settings['soc_0'])) $updateData['soc_0'] = $settings['soc_0'];
        if (isset($settings['cell_rcv'])) $updateData['cell_rcv'] = $settings['cell_rcv'];
        if (isset($settings['cell_rfv'])) $updateData['cell_rfv'] = $settings['cell_rfv'];
        if (isset($settings['system_power_off'])) $updateData['system_power_off'] = $settings['system_power_off'];
        if (isset($settings['charge_coc'])) $updateData['charge_coc'] = $settings['charge_coc'];
        if (isset($settings['discharge_coc'])) $updateData['discharge_coc'] = $settings['discharge_coc'];
        if (isset($settings['max_balance_current'])) $updateData['max_balance_current'] = $settings['max_balance_current'];
        if (isset($settings['charge_otp'])) $updateData['charge_otp'] = $settings['charge_otp'];
        if (isset($settings['charge_otpr'])) $updateData['charge_otpr'] = $settings['charge_otpr'];
        if (isset($settings['discharge_otp'])) $updateData['discharge_otp'] = $settings['discharge_otp'];
        if (isset($settings['discharge_otpr'])) $updateData['discharge_otpr'] = $settings['discharge_otpr'];
        if (isset($settings['charge_utp'])) $updateData['charge_utp'] = $settings['charge_utp'];
        if (isset($settings['charge_utpr'])) $updateData['charge_utpr'] = $settings['charge_utpr'];
        if (isset($settings['mos_otp'])) $updateData['mos_otp'] = $settings['mos_otp'];
        if (isset($settings['mos_otpr'])) $updateData['mos_otpr'] = $settings['mos_otpr'];
        if (isset($settings['cell_count'])) $updateData['cell_count'] = $settings['cell_count'];
        if (isset($settings['battery_capacity'])) $updateData['battery_capacity'] = $settings['battery_capacity'];
        if (isset($settings['balance_start_voltage'])) $updateData['balance_start_voltage'] = $settings['balance_start_voltage'];
        
        if (!empty($updateData)) {
            $this->update($updateData);
        }
    }
}
