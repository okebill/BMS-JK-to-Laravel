<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsCommandQueue extends Model
{
    use HasFactory;

    protected $table = 'bms_commands_queue';

    protected $fillable = [
        'device_id',
        'command_type',
        'register_address',
        'command_data',
        'status',
        'error_message',
        'sent_at',
        'completed_at',
    ];

    protected $casts = [
        'command_data' => 'array',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get pending commands for device
     */
    public static function getPendingForDevice($deviceId)
    {
        return static::where('device_id', $deviceId)
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Mark command as sent
     */
    public function markAsSent()
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark command as completed
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark command as failed
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'completed_at' => now(),
        ]);
    }

    /**
     * Convert to ESP32 command format
     */
    public function toEsp32Command()
    {
        $command = [
            'type' => $this->command_type,
            'register' => $this->register_address,
        ];

        if ($this->command_type === 'bms_write_register') {
            $command['value'] = $this->command_data['value'] ?? $this->command_data;
        } elseif ($this->command_type === 'bms_write_multiple_registers') {
            $command['values'] = $this->command_data['values'] ?? $this->command_data;
        }

        return $command;
    }
}
