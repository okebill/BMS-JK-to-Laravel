<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceCommand;
use App\Models\MonitorLog;
use App\Models\BmsParameter;
use App\Models\BmsCommandQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MonitorController extends Controller
{
    /**
     * Store monitoring data from ESP32
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Inverter data sekarang opsional karena fokus utama ke BMS
            'inverter' => 'nullable|array',
            'inverter.pv_voltage' => 'nullable|numeric',
            'inverter.pv_current' => 'nullable|numeric',
            'inverter.ac_voltage' => 'nullable|numeric',
            'inverter.load_power' => 'nullable|numeric',
            
            'bms' => 'required|array',
            'bms.battery_voltage' => 'nullable|numeric',
            'bms.current' => 'nullable|numeric',
            'bms.soc' => 'nullable|integer|min:0|max:100',
            'bms.temperature' => 'nullable|numeric',
            'bms.cell_voltages' => 'nullable|array',
            'bms.cell_count' => 'nullable|integer',
            'bms.bms_settings' => 'nullable|array',
            
            'device_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Save monitoring data (BMS lengkap)
            $monitorLog = MonitorLog::create([
                // Inverter (opsional)
                'pv_voltage'           => $request->input('inverter.pv_voltage'),
                'pv_current'           => $request->input('inverter.pv_current'),
                'ac_voltage'           => $request->input('inverter.ac_voltage'),
                'load_power'           => $request->input('inverter.load_power'),

                // BMS Pack
                'battery_voltage'      => $request->input('bms.battery_voltage'),
                'battery_current'      => $request->input('bms.current'),
                'power'                => $request->input('bms.power'),
                'soc'                  => $request->input('bms.soc'),
                'battery_temperature'  => $request->input('bms.temperature'),
                'temperature2'         => $request->input('bms.temperature2'),

                // Capacity
                'remaining_capacity'   => $request->input('bms.remaining_capacity'),
                'nominal_capacity'     => $request->input('bms.nominal_capacity'),

                // Cycle
                'cycle_count'          => $request->input('bms.cycle_count'),
                'total_cycle_capacity' => $request->input('bms.total_cycle_capacity'),

                // Balance
                'balance_current'      => $request->input('bms.balance_current'),
                'is_balancing'         => $request->boolean('bms.is_balancing'),

                // Alarm & Status
                'alarm_flags'          => $request->input('bms.alarm_flags'),
                'alarm_text'           => $request->input('bms.alarm_text'),
                'alarm_is_real'        => $request->boolean('bms.alarm_is_real'),
                'mosfet_status'        => $request->input('bms.mosfet_status'),
                'mosfet_text'          => $request->input('bms.mosfet_text'),
                'mos_temp'             => $request->input('bms.mos_temp'),

                // Cells
                'cell_voltages'        => $request->input('bms.cell_voltages', []),
                'cell_resistances'     => $request->input('bms.cell_resistances', []),
                'cell_count'           => $request->input('bms.cell_count', 0),
                'cell_diff_mv'         => $request->input('bms.cell_diff_mv'),

                'device_id'            => $request->input('device_id'),
                'recorded_at'          => now(),
            ]);


            $deviceId = $request->input('device_id', 'ESP32-001');
            
            // Save BMS parameters if provided
            if ($request->has('bms.bms_settings')) {
                $bmsSettings = $request->input('bms.bms_settings');
                $bmsParameter = BmsParameter::getForDevice($deviceId);
                $bmsParameter->updateFromSettings($bmsSettings);
                
                Log::info('BMS parameters updated', [
                    'device_id' => $deviceId,
                    'parameters_count' => count($bmsSettings),
                ]);
            }

            // Get pending commands from BMS command queue
            $pendingCommands = BmsCommandQueue::getPendingForDevice($deviceId);

            // Prepare response with commands
            $response = [
                'success' => true,
                'message' => 'Data saved successfully',
                'data_id' => $monitorLog->id,
                'commands' => [],
            ];

            // Add pending commands to response (format untuk ESP32)
            foreach ($pendingCommands as $command) {
                $esp32Command = $command->toEsp32Command();
                $response['commands'][] = $esp32Command;
                
                // Mark as sent
                $command->markAsSent();
            }

            Log::info('Monitoring data received', [
                'device_id' => $deviceId,
                'log_id' => $monitorLog->id,
                'commands_sent' => count($pendingCommands),
            ]);

            // Periodic cleanup: run every ~100 requests (random chance)
            if (rand(1, 100) === 1) {
                MonitorLog::cleanOldData(7, 50000);
                \App\Models\SystemLog::cleanOldLogs();
            }

            return response()->json($response, 201);

        } catch (\Exception $e) {
            Log::error('Error saving monitoring data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get latest monitoring data
     */
    public function latest()
    {
        $latest = MonitorLog::getLatest();

        if (!$latest) {
            return response()->json([
                'success' => false,
                'message' => 'No data available',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $latest,
        ]);
    }

    /**
     * Get chart data — last N hours, max 120 points
     * GET /api/monitor/chart?hours=1
     */
    public function chart(Request $request)
    {
        $hours     = (int) $request->query('hours', 1);
        $hours     = max(1, min($hours, 168));   // clamp 1–168 jam
        $maxPoints = 120;

        $rows = MonitorLog::getChartData($hours, $maxPoints);

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'labels'  => [],
                'datasets'=> [],
            ]);
        }

        $labels   = [];
        $soc      = [];
        $voltage  = [];
        $current  = [];
        $power    = [];
        $temp1    = [];
        $temp2    = [];
        $balance  = [];

        foreach ($rows as $r) {
            $labels[]  = $r->recorded_at->format('H:i:s');
            $soc[]     = $r->soc      ?? null;
            $voltage[] = $r->battery_voltage  !== null ? (float)$r->battery_voltage  : null;
            $current[] = $r->battery_current  !== null ? (float)$r->battery_current  : null;
            $power[]   = $r->power            !== null ? (float)$r->power            : null;
            $temp1[]   = $r->battery_temperature !== null ? (float)$r->battery_temperature : null;
            $temp2[]   = $r->temperature2     !== null ? (float)$r->temperature2     : null;
            $balance[] = $r->balance_current  !== null ? (float)$r->balance_current  : null;
        }

        return response()->json([
            'success' => true,
            'hours'   => $hours,
            'points'  => count($labels),
            'labels'  => $labels,
            'soc'     => $soc,
            'voltage' => $voltage,
            'current' => $current,
            'power'   => $power,
            'temp1'   => $temp1,
            'temp2'   => $temp2,
            'balance' => $balance,
        ]);
    }
}
