<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white">

    {{-- ============ MOBILE TOPBAR ============ --}}
    <div class="lg:hidden fixed top-0 inset-x-0 z-40 bg-gray-900/95 backdrop-blur border-b border-cyan-500/20 px-4 py-3 flex items-center justify-between">
        <button onclick="toggleSidebar()" class="p-2 rounded-lg bg-gray-800/80 border border-cyan-500/30 text-cyan-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h1 class="text-lg font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">BMS Settings</h1>
        <div class="w-9"></div>
    </div>

    {{-- ============ SIDEBAR OVERLAY ============ --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    {{-- ============ SIDEBAR ============ --}}
    <div id="sidebar-panel" class="sidebar-panel sidebar-closed lg:!transform-none fixed left-0 top-0 h-full w-64 bg-gray-900/98 backdrop-blur border-r border-cyan-500/20 z-50 lg:z-30">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8 lg:justify-start lg:gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">BMS Monitor</span>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav class="space-y-1">
                @php
                    $navItems = [
                        ['route' => 'dashboard',     'label' => 'Dashboard',      'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ['route' => 'control',       'label' => 'Control Panel',  'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
                        ['route' => 'bms-settings',  'label' => 'BMS Settings',   'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['route' => 'serial-monitor','label' => 'Serial Monitor', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ];
                @endphp
                @foreach($navItems as $nav)
                    @php $active = request()->routeIs($nav['route']); @endphp
                    <a href="{{ route($nav['route']) }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ $active ? 'bg-cyan-500/20 border border-cyan-500/30 text-cyan-400' : 'hover:bg-gray-800/50 text-gray-400 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $nav['icon'] }}"/>
                        </svg>
                        <span class="text-sm font-medium">{{ $nav['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
        <div class="absolute bottom-0 inset-x-0 p-6 border-t border-gray-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="lg:ml-64 p-4 md:p-6 lg:p-8 pt-16 lg:pt-8">

        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-black bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-400 bg-clip-text text-transparent">
                    ⚙️ BMS Settings
                </h2>
                <p class="text-gray-400 text-sm mt-1">Configure JK-BMS parameters via Modbus RTU &mdash; changes sent to ESP32</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" wire:model.lazy="deviceId" id="device-id-input"
                       class="bg-gray-800/60 border border-gray-700 rounded-xl px-4 py-2 text-sm text-white focus:border-cyan-500 outline-none w-36"
                       placeholder="ESP32-001">
                <button wire:click="loadParameters" id="reload-btn"
                        class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reload
                </button>
            </div>
        </div>

        {{-- Flash message --}}
        @if($flash)
        <div class="mb-5 p-4 rounded-2xl text-sm font-medium border {{ $flashType === 'success' ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-300' : 'bg-red-500/15 border-red-500/40 text-red-300' }}">
            {{ $flash }}
        </div>
        @endif

        {{-- Warning if no data --}}
        @if(!$cellCount && !$batteryCapacity && !$cellOvp)
        <div class="mb-5 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-sm flex gap-3">
            <span class="text-xl flex-shrink-0">⚠️</span>
            <div>
                <div class="font-semibold mb-1">Data BMS belum tersedia</div>
                <div class="text-amber-300/70">Settings akan muncul otomatis setelah ESP32 berhasil membaca parameter dari BMS (± 60 detik pertama). Pastikan BMS terhubung ke ESP32.</div>
            </div>
        </div>
        @endif

        {{-- ===== TABS (mirip JK-BMS App: Settings | Control) ===== --}}
        <div class="mb-6 flex gap-1 bg-gray-800/60 p-1 rounded-2xl border border-gray-700">
            @foreach([
                ['key'=>'basic',   'label'=>'🔋 Basic'],
                ['key'=>'advance', 'label'=>'⚡ Advance'],
                ['key'=>'system',  'label'=>'🖥️ System'],
                ['key'=>'wire',    'label'=>'🔌 Wire Res.'],
                ['key'=>'control', 'label'=>'🕹️ Control'],
            ] as $tab)
            <button wire:click="setActiveTab('{{ $tab['key'] }}')" id="tab-{{ $tab['key'] }}"
                    class="flex-1 py-2.5 px-2 rounded-xl text-xs sm:text-sm font-semibold transition-all {{ $activeTab === $tab['key'] ? 'bg-cyan-600 text-white shadow-lg shadow-cyan-500/30' : 'text-gray-400 hover:text-white' }}">
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        {{-- ==================== TAB: BASIC SETTINGS ==================== --}}
        @if($activeTab === 'basic')
        <div class="space-y-4">

            @php
                // Section header helper
                $sectionStyle = 'text-xs font-bold uppercase tracking-widest text-cyan-400 mb-3 mt-1';
            @endphp

            {{-- Basic Settings --}}
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-cyan-500/20 overflow-hidden">
                <div class="px-5 py-3 bg-cyan-500/10 border-b border-cyan-500/20">
                    <h3 class="text-sm font-bold text-cyan-400 uppercase tracking-wider">Basic Settings</h3>
                </div>
                <div class="p-4 space-y-3">
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'cell_count',
                        'label' => 'Cell Count',
                        'value' => $cellCount,
                        'unit'  => '',
                        'step'  => '1',
                        'min'   => '1', 'max' => '32',
                        'hint'  => 'Number of cells in series',
                    ])
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'battery_capacity',
                        'label' => 'Battery Capacity (Ah)',
                        'value' => $batteryCapacity,
                        'unit'  => 'Ah',
                        'step'  => '1',
                        'min'   => '1', 'max' => '9999',
                        'hint'  => 'Nominal battery capacity',
                    ])
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'balance_trigger',
                        'label' => 'Balance Trig. Volt. (V)',
                        'value' => $balanceTrigger,
                        'unit'  => 'V',
                        'step'  => '0.001',
                        'min'   => '0', 'max' => '0.500',
                        'hint'  => 'Cell difference to start balancing',
                    ])
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'balance_start_voltage',
                        'label' => 'Start Balance Volt. (V)',
                        'value' => $balanceStartVolt,
                        'unit'  => 'V',
                        'step'  => '0.001',
                        'min'   => '2', 'max' => '4.5',
                        'hint'  => 'Minimum cell voltage to start balancing',
                    ])
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'max_balance_current',
                        'label' => 'Max Balance Cur (A)',
                        'value' => $maxBalanceCurrent,
                        'unit'  => 'A',
                        'step'  => '0.001',
                        'min'   => '0', 'max' => '5',
                        'hint'  => 'Maximum balancing current',
                    ])
                </div>
            </div>

            {{-- SOC Reference --}}
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-emerald-500/20 overflow-hidden">
                <div class="px-5 py-3 bg-emerald-500/10 border-b border-emerald-500/20">
                    <h3 class="text-sm font-bold text-emerald-400 uppercase tracking-wider">SOC Calibration</h3>
                </div>
                <div class="p-4 space-y-3">
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'soc_100',
                        'label' => 'SOC-100% Volt. (V)',
                        'value' => $soc100,
                        'unit'  => 'V',
                        'step'  => '0.001',
                        'min'   => '3', 'max' => '4.5',
                        'hint'  => 'Cell voltage = 100% SOC',
                    ])
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'soc_0',
                        'label' => 'SOC-0% Volt. (V)',
                        'value' => $soc0,
                        'unit'  => 'V',
                        'step'  => '0.001',
                        'min'   => '2', 'max' => '3.5',
                        'hint'  => 'Cell voltage = 0% SOC',
                    ])
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'cell_rcv',
                        'label' => 'Vol. Cell RCV (V)',
                        'value' => $cellRcv,
                        'unit'  => 'V',
                        'step'  => '0.001',
                        'min'   => '2', 'max' => '4',
                        'hint'  => 'Cell Recovery Voltage',
                    ])
                    @include('livewire.partials.bms-setting-row', [
                        'id'    => 'cell_rfv',
                        'label' => 'Cell RFV (V)',
                        'value' => $cellRfv,
                        'unit'  => 'V',
                        'step'  => '0.001',
                        'min'   => '2', 'max' => '4',
                        'hint'  => 'Cell Resting Full Voltage',
                    ])
                </div>
            </div>

        </div>
        @endif

        {{-- ==================== TAB: ADVANCE SETTINGS ==================== --}}
        @if($activeTab === 'advance')
        <div class="space-y-4">

            {{-- Voltage Protection --}}
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-red-500/20 overflow-hidden">
                <div class="px-5 py-3 bg-red-500/10 border-b border-red-500/20">
                    <h3 class="text-sm font-bold text-red-400 uppercase tracking-wider">Cell Voltage Protection</h3>
                </div>
                <div class="p-4 space-y-3">
                    @include('livewire.partials.bms-setting-row', ['id'=>'cell_ovp',  'label'=>'Cell OVP (V)',      'value'=>$cellOvp,      'unit'=>'V', 'step'=>'0.001', 'min'=>'3',   'max'=>'5',   'hint'=>'Over Voltage Protection'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'cell_ovpr', 'label'=>'Vol. Cell RCV (V)', 'value'=>$cellOvpr,     'unit'=>'V', 'step'=>'0.001', 'min'=>'3',   'max'=>'4.5', 'hint'=>'OVP Recovery Voltage'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'cell_uvp',  'label'=>'Cell UVP (V)',      'value'=>$cellUvp,      'unit'=>'V', 'step'=>'0.001', 'min'=>'2',   'max'=>'3.5', 'hint'=>'Under Voltage Protection'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'cell_uvpr', 'label'=>'Cell UVPR (V)',     'value'=>$cellUvpr,     'unit'=>'V', 'step'=>'0.001', 'min'=>'2',   'max'=>'3.5', 'hint'=>'UVP Recovery Voltage'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'system_power_off','label'=>'Power Off Vol. (V)','value'=>$systemPowerOff,'unit'=>'V','step'=>'0.001','min'=>'2','max'=>'3.5','hint'=>'System complete shutdown voltage'])
                </div>
            </div>

            {{-- Current Protection --}}
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-orange-500/20 overflow-hidden">
                <div class="px-5 py-3 bg-orange-500/10 border-b border-orange-500/20">
                    <h3 class="text-sm font-bold text-orange-400 uppercase tracking-wider">Current Protection</h3>
                </div>
                <div class="p-4 space-y-3">
                    @include('livewire.partials.bms-setting-row', ['id'=>'charge_coc',         'label'=>'Continued Charge Curr (A)',      'value'=>$chargeCoc,         'unit'=>'A', 'step'=>'0.1', 'min'=>'0','max'=>'500','hint'=>'Charge overcurrent protection'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'charge_ocp_delay',   'label'=>'Charge OCP Delay (s)',           'value'=>$chargeOcpDelay,    'unit'=>'s', 'step'=>'1',   'min'=>'0','max'=>'600','hint'=>'Delay before OCP triggers'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'charge_ocpr_time',   'label'=>'Charge OCPR Time (s)',           'value'=>$chargeOcprTime,    'unit'=>'s', 'step'=>'1',   'min'=>'0','max'=>'600','hint'=>'OCP recovery time'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'discharge_coc',      'label'=>'Continued Discharge Curr (A)',   'value'=>$dischargeCoc,      'unit'=>'A', 'step'=>'0.1', 'min'=>'0','max'=>'500','hint'=>'Discharge overcurrent protection'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'discharge_ocp_delay','label'=>'Discharge OCP Delay (s)',        'value'=>$dischargeOcpDelay, 'unit'=>'s', 'step'=>'1',   'min'=>'0','max'=>'600','hint'=>'Delay before discharge OCP'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'discharge_ocpr_time','label'=>'Discharge OCPR Time (s)',        'value'=>$dischargeOcprTime, 'unit'=>'s', 'step'=>'1',   'min'=>'0','max'=>'600','hint'=>'Discharge OCP recovery time'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'scp_delay',          'label'=>'SCP Delay (μs)',                 'value'=>$scpDelay,          'unit'=>'μs','step'=>'1',   'min'=>'0','max'=>'100','hint'=>'Short circuit protection delay'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'scpr_time',          'label'=>'SCPR Time (s)',                  'value'=>$scprTime,          'unit'=>'s', 'step'=>'1',   'min'=>'0','max'=>'100','hint'=>'SCP recovery time'])
                </div>
            </div>

            {{-- Temperature Protection --}}
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-yellow-500/20 overflow-hidden">
                <div class="px-5 py-3 bg-yellow-500/10 border-b border-yellow-500/20">
                    <h3 class="text-sm font-bold text-yellow-400 uppercase tracking-wider">Temperature Protection</h3>
                </div>
                <div class="p-4">
                    {{-- Two columns on wider screens --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                        <div class="space-y-3">
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mb-1">Discharge</p>
                            @include('livewire.partials.bms-setting-row', ['id'=>'discharge_otp',  'label'=>'Discharge OTP (°C)',  'value'=>$dischargeOtp,  'unit'=>'°C', 'step'=>'0.1','min'=>'-20','max'=>'120','hint'=>'Over Temp Protection'])
                            @include('livewire.partials.bms-setting-row', ['id'=>'discharge_otpr', 'label'=>'Discharge OTPR (°C)', 'value'=>$dischargeOtpr, 'unit'=>'°C', 'step'=>'0.1','min'=>'-20','max'=>'120','hint'=>'OTP Recovery'])
                            @include('livewire.partials.bms-setting-row', ['id'=>'discharge_utpr', 'label'=>'Discharge UTPR (°C)','value'=>$dischargeUtpr, 'unit'=>'°C', 'step'=>'0.1','min'=>'-60','max'=>'0',  'hint'=>'Under Temp Protection Recovery'])
                            @include('livewire.partials.bms-setting-row', ['id'=>'discharge_utp',  'label'=>'Discharge UTP (°C)', 'value'=>$dischargeUtp,  'unit'=>'°C', 'step'=>'0.1','min'=>'-60','max'=>'0',  'hint'=>'Under Temperature Protection'])
                        </div>
                        <div class="space-y-3">
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mb-1">Charge</p>
                            @include('livewire.partials.bms-setting-row', ['id'=>'charge_otp',  'label'=>'Charge OTP (°C)',  'value'=>$chargeOtp,  'unit'=>'°C', 'step'=>'0.1','min'=>'-20','max'=>'120','hint'=>'Over Temp Protection'])
                            @include('livewire.partials.bms-setting-row', ['id'=>'charge_otpr', 'label'=>'Charge OTPR (°C)', 'value'=>$chargeOtpr, 'unit'=>'°C', 'step'=>'0.1','min'=>'-20','max'=>'120','hint'=>'OTP Recovery'])
                            @include('livewire.partials.bms-setting-row', ['id'=>'charge_utpr', 'label'=>'Charge UTPR (°C)','value'=>$chargeUtpr, 'unit'=>'°C', 'step'=>'0.1','min'=>'-60','max'=>'30', 'hint'=>'Under Temp Protection Recovery'])
                            @include('livewire.partials.bms-setting-row', ['id'=>'charge_utp',  'label'=>'Charge UTP (°C)', 'value'=>$chargeUtp,  'unit'=>'°C', 'step'=>'0.1','min'=>'-60','max'=>'30', 'hint'=>'Under Temperature Protection'])
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mt-4 mb-1">MOSFET</p>
                            @include('livewire.partials.bms-setting-row', ['id'=>'mos_otp',  'label'=>'MOS OTP (°C)',  'value'=>$mosOtp,  'unit'=>'°C', 'step'=>'0.1','min'=>'0','max'=>'150','hint'=>'MOSFET Over Temp Protection'])
                            @include('livewire.partials.bms-setting-row', ['id'=>'mos_otpr', 'label'=>'MOS OTPR (°C)', 'value'=>$mosOtpr, 'unit'=>'°C', 'step'=>'0.1','min'=>'0','max'=>'150','hint'=>'MOSFET OTP Recovery'])
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @endif

        {{-- ==================== TAB: SYSTEM ==================== --}}
        @if($activeTab === 'system')
        <div class="space-y-4">
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-indigo-500/20 overflow-hidden">
                <div class="px-5 py-3 bg-indigo-500/10 border-b border-indigo-500/20">
                    <h3 class="text-sm font-bold text-indigo-400 uppercase tracking-wider">System Settings</h3>
                </div>
                <div class="p-4 space-y-3">
                    @include('livewire.partials.bms-setting-row', ['id'=>'smart_sleep',     'label'=>'Vol. Smart Sleep (V)',  'value'=>$smartSleep,    'unit'=>'V', 'step'=>'0.001','min'=>'2','max'=>'4.0','hint'=>'Cell voltage to enter smart sleep'])
                    @include('livewire.partials.bms-setting-row', ['id'=>'smart_sleep_time','label'=>'Time Smart Sleep (h)', 'value'=>$smartSleepTime,'unit'=>'h', 'step'=>'1',   'min'=>'0','max'=>'72', 'hint'=>'Hours before entering sleep mode'])
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-gray-600/30 overflow-hidden">
                <div class="px-5 py-3 bg-gray-700/30 border-b border-gray-600/30">
                    <h3 class="text-sm font-bold text-gray-300 uppercase tracking-wider">ℹ️ Read-Only Info</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div class="bg-gray-900/50 rounded-xl p-3 text-center">
                            <div class="text-xs text-gray-500 mb-1">Device</div>
                            <div class="font-bold text-cyan-400">{{ $deviceId }}</div>
                        </div>
                        <div class="bg-gray-900/50 rounded-xl p-3 text-center">
                            <div class="text-xs text-gray-500 mb-1">UART Protocol</div>
                            <div class="font-bold text-gray-300 text-xs">RS485 Modbus V1.0</div>
                        </div>
                        <div class="bg-gray-900/50 rounded-xl p-3 text-center">
                            <div class="text-xs text-gray-500 mb-1">Cell Count</div>
                            <div class="font-bold text-emerald-400">{{ $cellCount }}S</div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-amber-500/10 border border-amber-500/30 rounded-xl text-xs text-amber-300">
                        <strong>ℹ️ Catatan:</strong> Setting seperti Device Addr, Data Stored Period, User Private Data, LCD Buzzer tidak tersedia
                        melalui Modbus RTU standard. Konfigurasi tersebut hanya bisa diubah langsung dari aplikasi JK-BMS via Bluetooth.
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ==================== TAB: WIRE RESISTANCE ==================== --}}
        @if($activeTab === 'wire')
        <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-purple-500/20 overflow-hidden">
            <div class="px-5 py-3 bg-purple-500/10 border-b border-purple-500/20">
                <h3 class="text-sm font-bold text-purple-400 uppercase tracking-wider">🔌 Con. Wire Resistance Settings (mΩ)</h3>
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-400 mb-4">These are <strong>read-only</strong> values calibrated by the BMS hardware. They cannot be changed remotely via Modbus.</p>
                @php
                    $wireResistances = \App\Models\MonitorLog::where('device_id', $deviceId)
                        ->latest('recorded_at')
                        ->value('cell_resistances') ?? [];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @forelse($wireResistances as $i => $res)
                    <div class="bg-gray-900/60 rounded-xl p-3 text-center border border-gray-700/50">
                        <div class="text-xs text-gray-500 mb-1">Con. Wire Res. {{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="text-lg font-bold text-{{ $res > 1 ? 'yellow' : 'emerald' }}-400">
                            {{ number_format($res, 2) }}
                        </div>
                        <div class="text-xs text-gray-600">mΩ</div>
                    </div>
                    @empty
                    <div class="col-span-4 text-center py-8 text-gray-500">
                        <div class="text-3xl mb-2">📭</div>
                        <div>No cell resistance data received yet.</div>
                        <div class="text-xs text-gray-600 mt-1">Data will appear once ESP32 sends BMS readings.</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        {{-- ==================== TAB: CONTROL ==================== --}}
        @if($activeTab === 'control')
        <div class="space-y-4">

            {{-- Warning box --}}
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-2xl text-sm text-red-300 flex gap-3">
                <span class="text-2xl flex-shrink-0">⚠️</span>
                <div>
                    <div class="font-bold mb-1">Hati-hati — Kontrol Langsung ke BMS!</div>
                    <div class="text-red-300/70 text-xs">Mengubah switch ini akan langsung mengirim perintah ke BMS via ESP32. Pastikan Anda memahami dampaknya sebelum mengubah. Perintah akan diterima ESP32 pada check-in berikutnya (~8 detik).</div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-gray-700/50 overflow-hidden">
                <div class="px-5 py-3 bg-gray-700/30 border-b border-gray-600/30">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">🕹️ BMS Control Switches</h3>
                </div>
                <div class="p-3 divide-y divide-gray-800/60">

                    @php
                        $controls = [
                            ['key'=>'charge',        'label'=>'Charge',               'icon'=>'🔋', 'prop'=>'ctlCharge',          'color'=>'emerald', 'danger'=>true],
                            ['key'=>'discharge',     'label'=>'Discharge',            'icon'=>'⚡', 'prop'=>'ctlDischarge',       'color'=>'emerald', 'danger'=>true],
                            ['key'=>'balance',       'label'=>'Balance',              'icon'=>'⚖️', 'prop'=>'ctlBalance',         'color'=>'blue',    'danger'=>false],
                            ['key'=>'emergency',     'label'=>'Emergency',            'icon'=>'🚨', 'prop'=>'ctlEmergency',       'color'=>'red',     'danger'=>true],
                            ['key'=>'disable_temp',  'label'=>'Disable Temp. Sensor', 'icon'=>'🌡️', 'prop'=>'ctlDisableTempSensor','color'=>'yellow', 'danger'=>true],
                            ['key'=>'display_always','label'=>'Display Always On',    'icon'=>'🖥️', 'prop'=>'ctlDisplayAlwaysOn', 'color'=>'indigo',  'danger'=>false],
                            ['key'=>'smart_sleep',   'label'=>'Smart Sleep On',       'icon'=>'🌙', 'prop'=>'ctlSmartSleepOn',    'color'=>'purple',  'danger'=>false],
                            ['key'=>'timed_stored',  'label'=>'Timed Stored Data',    'icon'=>'💾', 'prop'=>'ctlTimedStoredData', 'color'=>'cyan',    'danger'=>false],
                            ['key'=>'dry_arm',       'label'=>'DRY ARM Intermittent', 'icon'=>'🔔', 'prop'=>'ctlDryArmIntermit','color'=>'gray',     'danger'=>false],
                            ['key'=>'ocp2',          'label'=>'Discharge OCP 2',      'icon'=>'🛡️', 'prop'=>'ctlDischargeOcp2',  'color'=>'orange',  'danger'=>false],
                            ['key'=>'ocp3',          'label'=>'Discharge OCP 3',      'icon'=>'🛡️', 'prop'=>'ctlDischargeOcp3',  'color'=>'orange',  'danger'=>false],
                        ];
                    @endphp

                    @foreach($controls as $ctrl)
                    @php $isOn = $this->{$ctrl['prop']}; @endphp
                    <div class="flex items-center justify-between px-4 py-3.5 hover:bg-gray-800/30 transition rounded-xl">
                        <div class="flex items-center gap-3">
                            <span class="text-xl w-7 text-center">{{ $ctrl['icon'] }}</span>
                            <div>
                                <div class="text-sm font-semibold text-white">{{ $ctrl['label'] }}</div>
                                @if($ctrl['danger'])
                                <div class="text-xs text-red-400/70">⚠ Critical control</div>
                                @endif
                            </div>
                        </div>
                        <button id="ctrl-{{ $ctrl['key'] }}"
                                wire:click="toggleControl('{{ $ctrl['key'] }}')"
                                class="relative w-14 h-7 rounded-full transition-all duration-300 flex items-center {{ $isOn ? 'bg-'.$ctrl['color'].'-500 shadow-lg shadow-'.$ctrl['color'].'-500/40' : 'bg-gray-700' }}">
                            <span class="absolute w-5 h-5 bg-white rounded-full shadow transition-all duration-300 {{ $isOn ? 'left-8' : 'left-1' }}"></span>
                        </button>
                    </div>
                    @endforeach

                </div>
            </div>

            {{-- Erase History / Time Cal --}}
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/60 rounded-2xl border border-red-900/30 overflow-hidden">
                <div class="px-5 py-3 bg-red-900/20 border-b border-red-900/30">
                    <h3 class="text-sm font-bold text-red-400 uppercase tracking-wider">⚡ System Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-xl">
                        <div>
                            <div class="text-sm font-semibold text-white">🕐 Time Calibration</div>
                            <div class="text-xs text-gray-500">Sync BMS clock to server time</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-cyan-400 font-mono">{{ now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') }}</div>
                            <div class="text-xs text-gray-600">WIB</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-900/50 rounded-xl">
                        <div>
                            <div class="text-sm font-semibold text-red-400">🗑️ Erase History Data</div>
                            <div class="text-xs text-gray-500">Clear all historical BMS data from server DB</div>
                        </div>
                        <button onclick="if(confirm('YAKIN ingin menghapus semua history data? Tindakan ini tidak bisa dibatalkan!')) { @this.call('eraseHistory') }"
                                class="px-4 py-1.5 bg-red-600/80 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition">
                            ERASE
                        </button>
                    </div>
                </div>
            </div>

        </div>
        @endif

    </div>{{-- /main content --}}

    {{-- ============ CONFIRM MODAL ============ --}}
    @if($confirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm" id="confirm-modal">
        <div class="bg-gray-900 border border-amber-500/40 rounded-2xl p-6 w-full max-w-sm mx-4 shadow-2xl">
            <div class="text-center mb-4">
                <div class="text-4xl mb-2">⚠️</div>
                <h3 class="text-lg font-bold text-white mb-1">Konfirmasi Perubahan</h3>
                <p class="text-sm text-gray-400">Anda akan mengubah parameter BMS:</p>
            </div>
            <div class="bg-gray-800/80 rounded-xl p-4 mb-5 text-center">
                <div class="text-xs text-gray-500 mb-1">{{ $pendingLabel }}</div>
                <div class="text-2xl font-black text-amber-400">{{ $pendingValue }}</div>
            </div>
            <div class="text-xs text-red-300/70 bg-red-500/10 border border-red-500/20 rounded-xl p-3 mb-5">
                ⚠️ Perubahan akan dikirim langsung ke BMS via ESP32. Pastikan nilai sudah benar sebelum konfirmasi.
            </div>
            <div class="flex gap-3">
                <button wire:click="cancelConfirm" id="cancel-confirm-btn"
                        class="flex-1 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl text-sm font-semibold transition">
                    Batal
                </button>
                <button wire:click="confirmWrite" id="confirm-write-btn"
                        class="flex-1 py-2.5 bg-amber-500 hover:bg-amber-400 text-black rounded-xl text-sm font-bold transition">
                    ✅ Kirim ke BMS
                </button>
            </div>
        </div>
    </div>
    @endif

</div>

@push('styles')
<style>
.sidebar-closed  { transform: translateX(-100%); transition: transform 0.3s ease; }
.sidebar-open    { transform: translateX(0);      transition: transform 0.3s ease; }
@media (min-width: 1024px) { .sidebar-panel { transform: none !important; } }
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@push('scripts')
<script>
function toggleSidebar() {
    const overlay = document.getElementById('sidebar-overlay');
    const panel   = document.getElementById('sidebar-panel');
    const isClosed = panel.classList.contains('sidebar-closed');
    panel.classList.toggle('sidebar-closed', !isClosed);
    panel.classList.toggle('sidebar-open',    isClosed);
    overlay.classList.toggle('hidden', !isClosed);
}

// Helper: call Livewire askConfirm from row buttons
function bmsAskWrite(paramId, label) {
    const input = document.getElementById('bmsval_' + paramId);
    if (!input) return;
    const val = input.value.trim();
    if (val === '' || isNaN(parseFloat(val))) {
        alert('Masukkan nilai yang valid!');
        return;
    }
    @this.call('askConfirm', paramId, val, label);
}
</script>
@endpush
