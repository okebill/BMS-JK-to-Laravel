<?php

namespace App\Livewire;

use App\Models\BmsParameter;
use App\Models\DeviceCommand;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class BmsFullSettings extends Component
{
    public string $deviceId     = 'ESP32-001';
    public string $activeTab    = 'basic';   // basic | advance | system | control | wire
    public string $flash        = '';
    public string $flashType    = 'success'; // success | error
    public bool   $confirmModal = false;
    public string $pendingParam = '';
    public string $pendingValue = '';
    public string $pendingLabel = '';

    // ===== BASIC SETTINGS =====
    public $cellCount         = 0;
    public $batteryCapacity   = 0;
    public $balanceTrigger    = 0;
    public $balanceStartVolt  = 0;
    public $maxBalanceCurrent = 0;

    // ===== CELL VOLTAGE PROTECTION =====
    public $cellOvp      = 0;
    public $cellOvpr     = 0;
    public $cellUvp      = 0;
    public $cellUvpr     = 0;
    public $soc100       = 0;
    public $soc0         = 0;
    public $cellRcv      = 0;
    public $cellRfv      = 0;
    public $systemPowerOff = 0;
    public $smartSleep   = 0;

    // ===== CURRENT PROTECTION =====
    public $chargeCoc          = 0;
    public $chargeOcpDelay     = 0;
    public $chargeOcprTime     = 0;
    public $dischargeCoc       = 0;
    public $dischargeOcpDelay  = 0;
    public $dischargeOcprTime  = 0;
    public $scpDelay           = 0;
    public $scprTime           = 0;

    // ===== TEMPERATURE PROTECTION =====
    public $dischargeOtp   = 0;
    public $dischargeOtpr  = 0;
    public $dischargeUtp   = 0;
    public $dischargeUtpr  = 0;
    public $chargeOtp      = 0;
    public $chargeOtpr     = 0;
    public $chargeUtp      = 0;
    public $chargeUtpr     = 0;
    public $mosOtp         = 0;
    public $mosOtpr        = 0;

    // ===== SYSTEM =====
    public $smartSleepTime = 0;

    // ===== CONTROL (toggle) =====
    public bool $ctlCharge       = true;
    public bool $ctlDischarge    = true;
    public bool $ctlBalance      = true;
    public bool $ctlEmergency    = false;
    public bool $ctlDisableTempSensor = false;
    public bool $ctlDisplayAlwaysOn   = true;
    public bool $ctlSmartSleepOn      = false;
    public bool $ctlTimedStoredData   = false;
    public bool $ctlDryArmIntermit    = false;
    public bool $ctlDischargeOcp2     = true;
    public bool $ctlDischargeOcp3     = true;

    /* ----------------------------------------------------------------
       Register Map: key => [address (hex), scale]
       scale: 1000 = x0.001, 10 = x0.1, 1 = raw integer
    ---------------------------------------------------------------- */
    private array $registerMap = [
        // Voltage settings (x0.001 V → *1000)
        'smart_sleep'          => [0x1000, 1000],
        'cell_uvp'             => [0x1004, 1000],
        'cell_uvpr'            => [0x1008, 1000],
        'cell_ovp'             => [0x100C, 1000],
        'cell_ovpr'            => [0x1010, 1000],
        'balance_trigger'      => [0x1014, 1000],
        'soc_100'              => [0x1018, 1000],
        'soc_0'                => [0x101C, 1000],
        'cell_rcv'             => [0x1020, 1000],
        'cell_rfv'             => [0x1024, 1000],
        'system_power_off'     => [0x1028, 1000],

        // Current (x0.001 A → *1000)
        'charge_coc'           => [0x102C, 1000],
        'charge_ocp_delay'     => [0x1030,    1],  // seconds
        'charge_ocpr_time'     => [0x1034,    1],  // seconds
        'discharge_coc'        => [0x1038, 1000],
        'discharge_ocp_delay'  => [0x103C,    1],
        'discharge_ocpr_time'  => [0x1040,    1],
        'scpr_time'            => [0x1044,    1],
        'max_balance_current'  => [0x1048, 1000],

        // Temperature (x0.1 °C → *10)
        'charge_otp'           => [0x104C,   10],
        'charge_otpr'          => [0x1050,   10],
        'discharge_otp'        => [0x1054,   10],
        'discharge_otpr'       => [0x1058,   10],
        'charge_utp'           => [0x105C,   10],
        'charge_utpr'          => [0x1060,   10],
        'mos_otp'              => [0x1064,   10],
        'mos_otpr'             => [0x1068,   10],

        // Battery
        'cell_count'           => [0x106C,    1],
        'battery_capacity'     => [0x107C, 1000],

        // Misc
        'scp_delay'            => [0x1080,    1],  // microseconds
        'balance_start_voltage'=> [0x1084, 1000],

        // Temperature (extra — discharge utp/utpr, estimated registers)
        'discharge_utp'        => [0x1088,   10],
        'discharge_utpr'       => [0x108C,   10],

        // System timing
        'smart_sleep_time'     => [0x1090,    1],  // hours × ?

        // Control toggles (single-reg write)
        'ctl_charge'           => [0x1F00,    1],
        'ctl_discharge'        => [0x1F01,    1],
        'ctl_balance'          => [0x1F02,    1],
        'ctl_emergency'        => [0x1F03,    1],
        'ctl_disable_temp'     => [0x1F04,    1],
        'ctl_display_always_on'=> [0x1F05,    1],
        'ctl_smart_sleep_on'   => [0x1F06,    1],
        'ctl_timed_stored_data'=> [0x1F07,    1],
        'ctl_dry_arm'          => [0x1F08,    1],
        'ctl_discharge_ocp2'   => [0x1F09,    1],
        'ctl_discharge_ocp3'   => [0x1F0A,    1],
    ];

    public function mount(): void
    {
        $this->loadParameters();
    }

    public function loadParameters(): void
    {
        try {
            $p = BmsParameter::getForDevice($this->deviceId);

            // Basic
            $this->cellCount         = $p->cell_count          ?? 0;
            $this->batteryCapacity   = $p->battery_capacity     ?? 0;
            $this->balanceTrigger    = $p->balance_trigger      ?? 0;
            $this->balanceStartVolt  = $p->balance_start_voltage ?? 0;
            $this->maxBalanceCurrent = $p->max_balance_current  ?? 0;

            // Voltage protection
            $this->cellOvp       = $p->cell_ovp        ?? 0;
            $this->cellOvpr      = $p->cell_ovpr       ?? 0;
            $this->cellUvp       = $p->cell_uvp        ?? 0;
            $this->cellUvpr      = $p->cell_uvpr       ?? 0;
            $this->soc100        = $p->soc_100         ?? 0;
            $this->soc0          = $p->soc_0           ?? 0;
            $this->cellRcv       = $p->cell_rcv        ?? 0;
            $this->cellRfv       = $p->cell_rfv        ?? 0;
            $this->systemPowerOff= $p->system_power_off ?? 0;
            $this->smartSleep    = $p->smart_sleep     ?? 0;

            // Current
            $this->chargeCoc         = $p->charge_coc         ?? 0;
            $this->chargeOcpDelay    = $p->charge_ocp_delay   ?? 0;
            $this->chargeOcprTime    = $p->charge_ocpr_time   ?? 0;
            $this->dischargeCoc      = $p->discharge_coc      ?? 0;
            $this->dischargeOcpDelay = $p->discharge_ocp_delay ?? 0;
            $this->dischargeOcprTime = $p->discharge_ocpr_time ?? 0;
            $this->scpDelay          = $p->scp_delay          ?? 0;
            $this->scprTime          = $p->scpr_time          ?? 0;

            // Temperature
            $this->chargeOtp     = $p->charge_otp      ?? 0;
            $this->chargeOtpr    = $p->charge_otpr     ?? 0;
            $this->chargeUtp     = $p->charge_utp      ?? 0;
            $this->chargeUtpr    = $p->charge_utpr     ?? 0;
            $this->dischargeOtp  = $p->discharge_otp   ?? 0;
            $this->dischargeOtpr = $p->discharge_otpr  ?? 0;
            $this->dischargeUtp  = $p->discharge_utp   ?? 0;
            $this->dischargeUtpr = $p->discharge_utpr  ?? 0;
            $this->mosOtp        = $p->mos_otp         ?? 0;
            $this->mosOtpr       = $p->mos_otpr        ?? 0;

            $this->flash = '';
        } catch (\Exception $e) {
            $this->flash     = 'Gagal memuat parameter: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    /** Tampilkan modal konfirmasi sebelum write */
    public function askConfirm(string $param, string $value, string $label): void
    {
        $this->pendingParam = $param;
        $this->pendingValue = $value;
        $this->pendingLabel = $label;
        $this->confirmModal = true;
    }

    public function cancelConfirm(): void
    {
        $this->confirmModal = false;
        $this->pendingParam = '';
        $this->pendingValue = '';
    }

    /** Eksekusi write setelah konfirmasi */
    public function confirmWrite(): void
    {
        $this->confirmModal = false;
        $this->writeParameter($this->pendingParam, $this->pendingValue);
        $this->pendingParam = '';
        $this->pendingValue = '';
    }

    /** Queue command ke ESP32 via device_commands table */
    public function writeParameter(string $paramName, $value): void
    {
        if (!isset($this->registerMap[$paramName])) {
            $this->flash = "Parameter '$paramName' tidak dikenal.";
            $this->flashType = 'error';
            return;
        }

        [$regAddr, $scale] = $this->registerMap[$paramName];
        $rawValue = (int) round((float)$value * $scale);

        // Clamp to unsigned 32-bit
        if ($rawValue < 0) $rawValue = 0;
        if ($rawValue > 0xFFFFFFFF) $rawValue = 0xFFFFFFFF;

        try {
            // ESP32 processCommands() expects type=bms_write_register, register, value
            \App\Models\BmsCommandQueue::create([
                'device_id'        => $this->deviceId,
                'command_type'     => 'bms_write_register',
                'register_address' => $regAddr,
                'command_data'     => [
                    'value'   => $rawValue,
                    'param'   => $paramName,
                    'display' => $value,
                ],
                'status'           => 'pending',
            ]);

            $regHex = '0x' . strtoupper(dechex($regAddr));
            $this->flash     = "✅ Perintah '{$paramName}' = {$value} dikirim (reg {$regHex}, raw={$rawValue}). ESP32 akan menulis dalam ~8 detik.";
            $this->flashType = 'success';

            Log::info("[BmsSettings] Write queued", [
                'device'  => $this->deviceId,
                'param'   => $paramName,
                'reg'     => $regHex,
                'raw'     => $rawValue,
                'display' => $value,
            ]);

        } catch (\Exception $e) {
            $this->flash     = '❌ Error: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }

    /** Hapus semua history data monitoring di server */
    public function eraseHistory(): void
    {
        try {
            \App\Models\MonitorLog::where('device_id', $this->deviceId)->delete();
            $this->flash     = '🗑️ Semua history data untuk ' . $this->deviceId . ' telah dihapus.';
            $this->flashType = 'success';
        } catch (\Exception $e) {
            $this->flash     = '❌ Gagal hapus history: ' . $e->getMessage();
            $this->flashType = 'error';
        }
    }


    /** Toggle switch untuk Control tab */
    public function toggleControl(string $key): void
    {
        $prop = 'ctl' . str_replace('_', '', ucwords($key, '_'));
        // Map key → property name
        $map = [
            'charge'         => 'ctlCharge',
            'discharge'      => 'ctlDischarge',
            'balance'        => 'ctlBalance',
            'emergency'      => 'ctlEmergency',
            'disable_temp'   => 'ctlDisableTempSensor',
            'display_always' => 'ctlDisplayAlwaysOn',
            'smart_sleep'    => 'ctlSmartSleepOn',
            'timed_stored'   => 'ctlTimedStoredData',
            'dry_arm'        => 'ctlDryArmIntermit',
            'ocp2'           => 'ctlDischargeOcp2',
            'ocp3'           => 'ctlDischargeOcp3',
        ];
        $propName = $map[$key] ?? null;
        if (!$propName) return;

        $this->$propName = !$this->$propName;
        $newVal = $this->$propName ? 1 : 0;

        $paramKey = 'ctl_' . $key;
        $this->writeParameter($paramKey, $newVal);
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->flash = '';
    }

    public function render()
    {
        return view('livewire.bms-full-settings')
            ->layout('components.layouts.app', ['title' => 'BMS Settings — Okenet BMS Monitoring']);
    }
}
