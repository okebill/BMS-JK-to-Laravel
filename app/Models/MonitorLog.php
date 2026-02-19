<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitorLog extends Model
{
    use HasFactory;

    protected $table = 'monitoring_logs';

    protected $fillable = [
        'pv_voltage',
        'pv_current',
        'ac_voltage',
        'load_power',
        'battery_voltage',
        'battery_current',
        'power',
        'soc',
        'battery_temperature',
        'temperature2',
        'mos_temp',
        'remaining_capacity',
        'nominal_capacity',
        'cycle_count',
        'total_cycle_capacity',
        'balance_current',
        'is_balancing',
        'alarm_flags',
        'alarm_text',
        'alarm_is_real',
        'mosfet_status',
        'mosfet_text',
        'cell_voltages',
        'cell_resistances',
        'cell_count',
        'cell_diff_mv',
        'device_id',
        'recorded_at',
    ];

    protected $casts = [
        'pv_voltage'           => 'decimal:2',
        'pv_current'           => 'decimal:2',
        'ac_voltage'           => 'decimal:2',
        'load_power'           => 'decimal:2',
        'battery_voltage'      => 'decimal:3',
        'battery_current'      => 'decimal:3',
        'power'                => 'decimal:2',
        'battery_temperature'  => 'decimal:1',
        'temperature2'         => 'decimal:1',
        'mos_temp'             => 'decimal:1',
        'remaining_capacity'   => 'decimal:3',
        'nominal_capacity'     => 'decimal:3',
        'total_cycle_capacity' => 'decimal:3',
        'balance_current'      => 'decimal:3',
        'is_balancing'         => 'boolean',
        'alarm_is_real'        => 'boolean',
        'cell_voltages'        => 'array',
        'cell_resistances'     => 'array',
        'recorded_at'          => 'datetime',
    ];

    /**
     * Get latest monitoring data
     * Only return data if it's within the last X minutes
     */
    public static function getLatest($maxAgeMinutes = 5)
    {
        return static::where('recorded_at', '>=', now()->subMinutes($maxAgeMinutes))
            ->latest('recorded_at')
            ->first();
    }
    
    /**
     * Get latest data regardless of age (for historical view)
     */
    public static function getLatestAny()
    {
        return static::latest('recorded_at')->first();
    }
    
    /**
     * Check if BMS is online (data received within last X minutes)
     * Optimized: uses single query with select only needed column
     */
    public static function isBmsOnline($maxAgeMinutes = 2)
    {
        return static::where('recorded_at', '>=', now()->subMinutes($maxAgeMinutes))
            ->exists();
    }

    /**
     * Get data for chart - OPTIMIZED
     * Uses database-level sampling instead of loading all records
     */
    public static function getChartData($hours = 24, $maxPoints = 100)
    {
        $query = static::where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at');
        
        $totalRecords = $query->count();
        
        if ($totalRecords == 0) {
            return collect();
        }
        
        $cols = ['id', 'battery_voltage', 'battery_current', 'power', 'soc',
                 'battery_temperature', 'temperature2', 'balance_current', 'recorded_at'];

        // If within limit, just get them
        if ($totalRecords <= $maxPoints) {
            return $query->select($cols)->get();
        }
        
        // Sample using modulo on ID for efficiency (database-level)
        $step = max(1, (int)ceil($totalRecords / $maxPoints));
        
        return static::where('recorded_at', '>=', now()->subHours($hours))
            ->whereRaw('id % ? = 0', [$step])
            ->orderBy('recorded_at')
            ->select($cols)
            ->limit($maxPoints)
            ->get();
    }

    /**
     * Clean old logs - keep only last N records or last X days
     */
    public static function cleanOldData($keepDays = 7, $maxRecords = 50000)
    {
        // Delete records older than X days
        $deleted = static::where('recorded_at', '<', now()->subDays($keepDays))->delete();
        
        // Also cap total records
        $count = static::count();
        if ($count > $maxRecords) {
            $toDelete = $count - $maxRecords;
            $oldestToKeep = static::orderBy('recorded_at', 'desc')
                ->skip($maxRecords)
                ->take(1)
                ->value('id');
            
            if ($oldestToKeep) {
                $deleted += static::where('id', '<=', $oldestToKeep)->delete();
            }
        }
        
        return $deleted;
    }
}
