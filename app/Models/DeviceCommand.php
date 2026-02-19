<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCommand extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'command_type',
        'command_data',
        'status',
        'sent_at',
        'executed_at',
        'response',
        'user_id',
    ];

    protected $casts = [
        'command_data' => 'array',
        'sent_at' => 'datetime',
        'executed_at' => 'datetime',
    ];

    /**
     * Get pending commands for a device
     */
    public static function getPendingForDevice($deviceId = null)
    {
        $query = static::where('status', 'pending')
            ->orderBy('created_at', 'asc');
            
        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }
        
        return $query->get();
    }

    /**
     * Mark command as sent
     */
    public function markAsSent($response = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'response' => $response,
        ]);
    }

    /**
     * Mark command as executed
     */
    public function markAsExecuted($response = null)
    {
        $this->update([
            'status' => 'executed',
            'executed_at' => now(),
            'response' => $response,
        ]);
    }

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
