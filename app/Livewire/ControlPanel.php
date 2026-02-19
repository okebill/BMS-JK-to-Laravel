<?php

namespace App\Livewire;

use App\Models\BmsSetting;
use App\Models\DeviceCommand;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ControlPanel extends Component
{
    // Inverter Configuration
    public $inverterModbusAddress = 1;
    public $inverterMaxChargingCurrent = 50;
    public $inverterMode = 'auto';
    
    // BMS Protection Settings
    public $cellVoltageOvervoltage = 3.750;
    public $cellVoltageUndervoltage = 2.800;
    public $cellVoltageOvervoltageRecovery = 3.500;
    public $cellVoltageUndervoltageRecovery = 2.900;
    public $cellVoltageBalanceStart = 3.400;
    public $cellVoltageBalanceDelta = 0.010;
    
    public $totalVoltageOvervoltage = 60.00;
    public $totalVoltageUndervoltage = 44.80;
    public $totalVoltageOvervoltageRecovery = 56.00;
    public $totalVoltageUndervoltageRecovery = 46.40;
    
    public $chargeOvercurrentProtection = 200;
    public $dischargeOvercurrentProtection = 200;
    public $chargeOvertemperatureProtection = 50;
    public $chargeUndertemperatureProtection = 0;
    public $dischargeOvertemperatureProtection = 60;
    public $dischargeUndertemperatureProtection = -20;
    
    // BMS Balance Settings
    public $balanceStartVoltage = 3400;
    public $balanceDeltaVoltage = 10;
    public $balanceEnabled = true;
    
    // BMS Device Info
    public $deviceName = '';
    public $manufacturingDate = '';
    public $totalRuntime = '';
    public $cycles = 0;
    
    // BMS Advanced
    public $currentCalibration = 0.000;
    public $sleepTime = 0;
    public $switchState = true;
    
    public $deviceId = 'ESP32-001';
    public $successMessage = '';
    public $errorMessage = '';
    public $activeTab = 'protection'; // protection, balance, device, advanced

    public function mount()
    {
        $this->loadBmsSettings();
    }

    public function loadBmsSettings()
    {
        try {
            $settings = BmsSetting::getForDevice($this->deviceId);
            
            // Protection Settings
            $this->cellVoltageOvervoltage = $settings->cell_voltage_overvoltage;
            $this->cellVoltageUndervoltage = $settings->cell_voltage_undervoltage;
            $this->cellVoltageOvervoltageRecovery = $settings->cell_voltage_overvoltage_recovery;
            $this->cellVoltageUndervoltageRecovery = $settings->cell_voltage_undervoltage_recovery;
            $this->cellVoltageBalanceStart = $settings->cell_voltage_balance_start;
            $this->cellVoltageBalanceDelta = $settings->cell_voltage_balance_delta;
            
            $this->totalVoltageOvervoltage = $settings->total_voltage_overvoltage;
            $this->totalVoltageUndervoltage = $settings->total_voltage_undervoltage;
            $this->totalVoltageOvervoltageRecovery = $settings->total_voltage_overvoltage_recovery;
            $this->totalVoltageUndervoltageRecovery = $settings->total_voltage_undervoltage_recovery;
            
            $this->chargeOvercurrentProtection = $settings->charge_overcurrent_protection;
            $this->dischargeOvercurrentProtection = $settings->discharge_overcurrent_protection;
            $this->chargeOvertemperatureProtection = $settings->charge_overtemperature_protection;
            $this->chargeUndertemperatureProtection = $settings->charge_undertemperature_protection;
            $this->dischargeOvertemperatureProtection = $settings->discharge_overtemperature_protection;
            $this->dischargeUndertemperatureProtection = $settings->discharge_undertemperature_protection;
            
            // Balance Settings
            $this->balanceStartVoltage = $settings->balance_start_voltage;
            $this->balanceDeltaVoltage = $settings->balance_delta_voltage;
            $this->balanceEnabled = $settings->balance_enabled;
            
            // Device Info
            $this->deviceName = $settings->device_name ?? '';
            $this->manufacturingDate = $settings->manufacturing_date ?? '';
            $this->totalRuntime = $settings->total_runtime ?? '';
            $this->cycles = $settings->cycles;
            
            // Advanced
            $this->currentCalibration = $settings->current_calibration;
            $this->sleepTime = $settings->sleep_time;
            $this->switchState = $settings->switch_state;
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error loading settings: ' . $e->getMessage();
        }
    }

    public function saveInverterConfig()
    {
        $this->validate([
            'inverterModbusAddress' => 'required|integer|min:1|max:247',
            'inverterMaxChargingCurrent' => 'required|numeric|min:0|max:200',
            'inverterMode' => 'required|in:auto,manual,grid,off',
        ]);

        try {
            DeviceCommand::create([
                'device_id' => $this->deviceId,
                'command_type' => 'inverter_config',
                'command_data' => [
                    'modbus_address' => $this->inverterModbusAddress,
                    'max_charging_current' => $this->inverterMaxChargingCurrent,
                    'mode' => $this->inverterMode,
                ],
                'status' => 'pending',
                'user_id' => Auth::id(),
            ]);

            $this->successMessage = 'Inverter configuration queued successfully!';
            $this->errorMessage = '';
            $this->dispatch('command-queued');
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function saveBmsProtectionSettings()
    {
        $this->validate([
            'cellVoltageOvervoltage' => 'required|numeric|min:2.5|max:4.5',
            'cellVoltageUndervoltage' => 'required|numeric|min:2.0|max:3.5',
            'cellVoltageOvervoltageRecovery' => 'required|numeric|min:2.5|max:4.5',
            'cellVoltageUndervoltageRecovery' => 'required|numeric|min:2.0|max:3.5',
            'cellVoltageBalanceStart' => 'required|numeric|min:3.0|max:4.0',
            'cellVoltageBalanceDelta' => 'required|numeric|min:0.001|max:0.1',
            'totalVoltageOvervoltage' => 'required|numeric|min:40|max:80',
            'totalVoltageUndervoltage' => 'required|numeric|min:30|max:60',
            'chargeOvercurrentProtection' => 'required|integer|min:0|max:500',
            'dischargeOvercurrentProtection' => 'required|integer|min:0|max:500',
        ]);

        try {
            $response = Http::post(config('app.url') . '/api/bms/settings', [
                'device_id' => $this->deviceId,
                'cell_voltage_overvoltage' => $this->cellVoltageOvervoltage,
                'cell_voltage_undervoltage' => $this->cellVoltageUndervoltage,
                'cell_voltage_overvoltage_recovery' => $this->cellVoltageOvervoltageRecovery,
                'cell_voltage_undervoltage_recovery' => $this->cellVoltageUndervoltageRecovery,
                'cell_voltage_balance_start' => $this->cellVoltageBalanceStart,
                'cell_voltage_balance_delta' => $this->cellVoltageBalanceDelta,
                'total_voltage_overvoltage' => $this->totalVoltageOvervoltage,
                'total_voltage_undervoltage' => $this->totalVoltageUndervoltage,
                'charge_overcurrent_protection' => $this->chargeOvercurrentProtection,
                'discharge_overcurrent_protection' => $this->dischargeOvercurrentProtection,
            ]);

            if ($response->successful()) {
                $this->successMessage = 'BMS Protection Settings updated and commands queued!';
                $this->errorMessage = '';
                $this->dispatch('settings-updated');
            } else {
                $this->errorMessage = 'Error: ' . $response->json()['message'] ?? 'Unknown error';
                $this->successMessage = '';
            }
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function saveBmsBalanceSettings()
    {
        $this->validate([
            'balanceStartVoltage' => 'required|integer|min:3000|max:4000',
            'balanceDeltaVoltage' => 'required|integer|min:1|max:100',
            'balanceEnabled' => 'boolean',
        ]);

        try {
            $response = Http::post(config('app.url') . '/api/bms/settings', [
                'device_id' => $this->deviceId,
                'balance_start_voltage' => $this->balanceStartVoltage,
                'balance_delta_voltage' => $this->balanceDeltaVoltage,
                'balance_enabled' => $this->balanceEnabled,
            ]);

            if ($response->successful()) {
                $this->successMessage = 'BMS Balance Settings updated!';
                $this->errorMessage = '';
                $this->dispatch('settings-updated');
            } else {
                $this->errorMessage = 'Error: ' . $response->json()['message'] ?? 'Unknown error';
                $this->successMessage = '';
            }
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.control-panel')
            ->layout('components.layouts.app', ['title' => 'Control Panel — Okenet BMS Monitoring']);
    }
}
