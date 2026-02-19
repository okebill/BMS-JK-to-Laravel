<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BmsSetting;
use App\Models\DeviceCommand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BmsCommandController extends Controller
{
    /**
     * Update BMS settings and send command to ESP32
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'nullable|string|max:50',
            'cell_voltage_overvoltage' => 'nullable|numeric|min:2.5|max:4.5',
            'cell_voltage_undervoltage' => 'nullable|numeric|min:2.0|max:3.5',
            'cell_voltage_balance_start' => 'nullable|numeric|min:3.0|max:4.0',
            'cell_voltage_balance_delta' => 'nullable|numeric|min:0.001|max:0.1',
            'total_voltage_overvoltage' => 'nullable|numeric|min:40|max:80',
            'total_voltage_undervoltage' => 'nullable|numeric|min:30|max:60',
            'charge_overcurrent_protection' => 'nullable|integer|min:0|max:500',
            'discharge_overcurrent_protection' => 'nullable|integer|min:0|max:500',
            'balance_enabled' => 'nullable|boolean',
            'switch_state' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $deviceId = $request->input('device_id', 'ESP32-001');
            $settings = BmsSetting::getForDevice($deviceId);
            
            // Update settings
            $updateData = $request->only([
                'cell_voltage_overvoltage',
                'cell_voltage_undervoltage',
                'cell_voltage_overvoltage_recovery',
                'cell_voltage_undervoltage_recovery',
                'cell_voltage_balance_start',
                'cell_voltage_balance_delta',
                'total_voltage_overvoltage',
                'total_voltage_undervoltage',
                'charge_overcurrent_protection',
                'discharge_overcurrent_protection',
                'balance_enabled',
                'switch_state',
            ]);
            
            $settings->update($updateData);
            
            // Create device command untuk dikirim ke ESP32
            $commands = [];
            
            // Convert settings to Modbus write commands
            if ($request->has('cell_voltage_overvoltage')) {
                $commands[] = [
                    'type' => 'bms_write_register',
                    'register' => $settings->reg_cell_overvoltage,
                    'value' => (int)($request->input('cell_voltage_overvoltage') * 1000), // Convert to mV
                ];
            }
            
            if ($request->has('cell_voltage_undervoltage')) {
                $commands[] = [
                    'type' => 'bms_write_register',
                    'register' => $settings->reg_cell_undervoltage,
                    'value' => (int)($request->input('cell_voltage_undervoltage') * 1000), // Convert to mV
                ];
            }
            
            if ($request->has('cell_voltage_balance_start')) {
                $commands[] = [
                    'type' => 'bms_write_register',
                    'register' => $settings->reg_balance_start,
                    'value' => (int)($request->input('cell_voltage_balance_start') * 1000), // Convert to mV
                ];
            }
            
            if ($request->has('cell_voltage_balance_delta')) {
                $commands[] = [
                    'type' => 'bms_write_register',
                    'register' => $settings->reg_balance_delta,
                    'value' => (int)($request->input('cell_voltage_balance_delta') * 1000), // Convert to mV
                ];
            }
            
            // Save commands to database
            foreach ($commands as $cmd) {
                DeviceCommand::create([
                    'device_id' => $deviceId,
                    'command_type' => $cmd['type'],
                    'command_data' => [
                        'register' => $cmd['register'],
                        'value' => $cmd['value'],
                    ],
                    'status' => 'pending',
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'BMS settings updated and commands queued',
                'settings' => $settings,
                'commands_queued' => count($commands),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current BMS settings
     */
    public function getSettings(Request $request)
    {
        $deviceId = $request->input('device_id', 'ESP32-001');
        $settings = BmsSetting::getForDevice($deviceId);
        
        return response()->json([
            'success' => true,
            'settings' => $settings,
        ], 200);
    }
}
