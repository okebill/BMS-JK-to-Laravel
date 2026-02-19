<?php

namespace App\Livewire;

use App\Models\SystemLog;
use Livewire\Component;

class RemoteTerminal extends Component
{
    public $logs = [];
    public $isLive = true;
    
    public function mount()
    {
        $this->loadLogs();
    }

    public function loadLogs()
    {
        $this->logs = SystemLog::getLatestLogs(30)
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'message' => $log->message,
                    'level' => $log->level,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'time' => $log->created_at->format('H:i:s'),
                    'date' => $log->created_at->format('Y-m-d'),
                ];
            })
            ->toArray();
        
        // Dispatch event untuk auto-scroll
        $this->dispatch('logs-updated');
    }

    public function clearLogs()
    {
        // Delete all logs
        SystemLog::truncate();
        $this->loadLogs();
        $this->dispatch('logs-cleared');
    }

    public function render()
    {
        return view('livewire.remote-terminal')
            ->layout('components.layouts.app', ['title' => 'Serial Monitor — Okenet BMS Monitoring']);
    }
}
