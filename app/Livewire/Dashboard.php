<?php

namespace App\Livewire;

use App\Models\MonitorLog;
use Livewire\Component;
use Livewire\Attributes\On;

class Dashboard extends Component
{
    public $latestData;
    public $isBmsOnline = false;
    public $lastUpdateTime = null;
    
    public function mount()
    {
        $this->loadData();
        
        // Clean old data on page load (housekeeping)
        MonitorLog::cleanOldData(7, 50000);
    }

    public function loadData()
    {
        // Single query: get latest record (covers latestData + lastUpdateTime + isBmsOnline)
        $latest = MonitorLog::getLatestAny();
        
        if ($latest) {
            $this->lastUpdateTime = $latest->recorded_at;
            $this->isBmsOnline = $latest->recorded_at->isAfter(now()->subMinutes(2));
            $this->latestData = $this->isBmsOnline ? $latest : null;
        } else {
            $this->lastUpdateTime = null;
            $this->isBmsOnline = false;
            $this->latestData = null;
        }
    }

    #[On('data-updated')]
    public function refreshData()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('components.layouts.app', ['title' => 'Dashboard — Okenet BMS Monitoring']);
    }
}
