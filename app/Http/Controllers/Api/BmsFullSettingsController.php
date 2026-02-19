<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BmsParameter;
use App\Models\BmsCommandQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BmsFullSettingsController extends Controller
{
    /**
     * Get current BMS parameters
     */
    public function getParameters(Request $request)
    {
        $deviceId = $request->input('device_id', 'ESP32-001');
        $parameters = BmsParameter::getForDevice($deviceId);
        
        return response()->json([
            'success' => true,
            'parameters' => $parameters,
        ]);
    }

    /**
     * Update BMS parameter and queue write command
     */
    public function updateParameter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'parameter_name' => 'required|string',
            'value' => 'required|numeric',
            'register_address' => 'required|integer',
            'multiplier' => 'nullable|numeric|default:1000', // Default: mV (multiply by 1000)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $deviceId = $request->input('device_id');
            $parameterName = $request->input('parameter_name');
            $value = $request->input('value');
            $registerAddress = $request->input('register_address');
            $multiplier = $request->input('multiplier', 1000);

            // Calculate register value dengan multiplier
            $registerValue = (int)($value * $multiplier);

            // Update parameter in database first (if field exists)
            try {
                $parameter = BmsParameter::getForDevice($deviceId);
                $dbFieldName = $parameterName; // Field name should match
                if (in_array($dbFieldName, $parameter->getFillable())) {
                    $parameter->update([$dbFieldName => $value]);
                }
            } catch (\Exception $e) {
                // Continue even if update fails
                Log::warning('Failed to update parameter in database', ['error' => $e->getMessage()]);
            }

            // Create command in queue
            $command = BmsCommandQueue::create([
                'device_id' => $deviceId,
                'command_type' => 'bms_write_register',
                'register_address' => $registerAddress,
                'command_data' => ['value' => $registerValue],
                'status' => 'pending',
            ]);

            Log::info('BMS parameter update queued', [
                'device_id' => $deviceId,
                'parameter' => $parameterName,
                'value' => $value,
                'register' => $registerAddress,
                'register_value' => $registerValue,
                'command_id' => $command->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Parameter update queued successfully',
                'command_id' => $command->id,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error queuing BMS parameter update', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error queuing parameter update',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
