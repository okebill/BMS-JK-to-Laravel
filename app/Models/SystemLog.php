<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $table = 'system_logs';

    protected $fillable = [
        'message',
        'level',
        'device_id',
    ];

    /**
     * Get latest logs (limit 30 for terminal display)
     */
    public static function getLatestLogs($limit = 30)
    {
        return static::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Clean old logs, keep only latest 500
     */
    public static function cleanOldLogs()
    {
        $count = static::count();
        if ($count > 500) {
            $toDelete = $count - 500;
            static::orderBy('created_at', 'asc')
                ->limit($toDelete)
                ->delete();
        }
    }
}
