<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LogController extends Controller
{
    /**
     * Store log from ESP32
     * No CSRF protection needed for API endpoint
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'level' => 'nullable|string|in:info,error,warning,debug',
            'device_id' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Save log
            $systemLog = SystemLog::create([
                'message' => $request->input('message'),
                'level' => $request->input('level', 'info'),
                'device_id' => $request->input('device_id'),
            ]);

            // Clean old logs periodically (keep only 500 latest)
            // Only clean every 10th log to avoid performance issues
            if ($systemLog->id % 10 == 0) {
                SystemLog::cleanOldLogs();
            }

            return response()->json([
                'success' => true,
                'message' => 'Log saved successfully',
                'id' => $systemLog->id,
            ], 201);

        } catch (\Exception $e) {
            // Don't log to Laravel log to avoid recursion
            return response()->json([
                'success' => false,
                'message' => 'Error saving log',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
