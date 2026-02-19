<div class="min-h-screen bg-gradient-to-br from-gray-950 via-gray-900 to-black text-white"
     wire:poll.8s="loadData">

    {{-- Mobile Top Bar --}}
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-gray-950/95 backdrop-blur-lg border-b border-emerald-500/20 px-4 py-3 flex items-center justify-between">
        <button onclick="toggleSidebar()" class="p-2 rounded-lg bg-gray-800/80 border border-emerald-500/30 text-emerald-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <h1 class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">⚡ BMS Monitor</h1>
        <div class="w-9"></div>
    </div>

    {{-- Sidebar Overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    {{-- ========== SIDEBAR ========== --}}
    <div id="sidebar-panel" class="sidebar-panel sidebar-closed lg:!transform-none fixed left-0 top-0 h-full w-64 bg-gray-950/98 backdrop-blur-lg border-r border-emerald-500/20 z-50 lg:z-30">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8 lg:justify-start lg:gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold bg-gradient-to-r from-emerald-400 to-cyan-400 bg-clip-text text-transparent">BMS Monitor</h1>
                        <p class="text-xs text-gray-500">ESP32-001</p>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav class="space-y-1.5">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('control') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-800/60 text-gray-400 hover:text-emerald-400 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Control Panel
                </a>
                <a href="{{ route('bms-settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-800/60 text-gray-400 hover:text-emerald-400 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    BMS Settings
                </a>
                <a href="{{ route('serial-monitor') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-800/60 text-gray-400 hover:text-emerald-400 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Serial Monitor
                </a>
            </nav>
        </div>
        <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-800/60">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/15 text-gray-400 hover:text-red-400 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ========================= MAIN CONTENT ========================= --}}
    <div class="lg:ml-64 p-4 md:p-6 pt-16 lg:pt-6">

        {{-- Header --}}
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl md:text-2xl font-bold tracking-tight">Okenet BMS Monitoring</h2>
                <p class="text-gray-400 text-sm mt-0.5">JK-BMS via ESP32 · Auto-refresh 8 s</p>
            </div>
            <div class="flex items-center gap-3">
                @if($isBmsOnline)
                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-500/15 border border-emerald-500/40 rounded-xl">
                        <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></div>
                        <span class="text-emerald-400 text-sm font-semibold">Online</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 px-4 py-2 bg-red-500/15 border border-red-500/40 rounded-xl">
                        <div class="w-2.5 h-2.5 bg-red-400 rounded-full"></div>
                        <span class="text-red-400 text-sm font-semibold">Offline</span>
                    </div>
                @endif
                @if($lastUpdateTime)
                    <div class="text-xs text-gray-500 hidden sm:block">Update: {{ $lastUpdateTime->format('d/m H:i:s') }}</div>
                @endif
            </div>
        </div>

        @if($latestData && $isBmsOnline)
        @php
            $d          = $latestData;
            $cells      = $d->cell_voltages    ?? [];
            $resistances= $d->cell_resistances ?? [];
            $cellCount  = intval($d->cell_count ?? count($cells));
            if ($cellCount > count($cells)) $cellCount = count($cells);
            $socPct     = intval($d->soc ?? 0);
            $socColor   = $socPct >= 70 ? 'emerald' : ($socPct >= 30 ? 'yellow' : 'red');
            $isCharging = floatval($d->power ?? 0) >= 0;
            $powerColor = $isCharging ? 'emerald' : 'orange';
            $powerDir   = $isCharging ? '↑ Charging' : '↓ Discharge';
            // Gunakan alarm_is_real dari ESP32 (lebih akurat)
            // Fallback: jika kolom belum ada, gunakan alarm_flags == 0
            $alarmIsReal = $d->alarm_is_real ?? (intval($d->alarm_flags ?? 0) !== 0);
            $alarmText   = $d->alarm_text ?? 'OK';
        @endphp

        {{-- ===== ROW 1: SOC + VITAL ===== --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-4">
            {{-- SOC --}}
            <div class="col-span-2 lg:col-span-1 relative bg-gradient-to-br from-gray-800/80 to-gray-900/80 rounded-2xl p-5 border border-{{ $socColor }}-500/30 overflow-hidden">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">State of Charge</div>
                <div class="text-5xl md:text-6xl font-black text-{{ $socColor }}-400 leading-none">
                    {{ $socPct }}<span class="text-2xl font-semibold">%</span>
                </div>
                <div class="mt-3 h-2.5 bg-gray-700 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $socColor }}-400 rounded-full transition-all duration-700"
                         style="width:{{ min($socPct,100) }}%"></div>
                </div>
                <div class="mt-2 flex justify-between text-xs text-gray-500">
                    <span>{{ number_format($d->remaining_capacity ?? 0, 1) }} Ah</span>
                    <span>/ {{ number_format($d->nominal_capacity ?? 0, 1) }} Ah</span>
                </div>
            </div>

            {{-- Voltage --}}
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-cyan-500/20 hover:border-cyan-500/40 transition-colors">
                <div class="text-xs text-gray-400 font-medium mb-1">Pack Voltage</div>
                <div class="text-2xl md:text-3xl font-bold text-cyan-400">
                    {{ number_format($d->battery_voltage ?? 0, 2) }}<span class="text-base font-normal text-gray-400"> V</span>
                </div>
                @if(count($cells))
                <div class="mt-2 text-xs text-gray-500">
                    {{ number_format(min($cells),3) }} – {{ number_format(max($cells),3) }} V
                </div>
                @endif
            </div>

            {{-- Current --}}
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-{{ $powerColor }}-500/20 hover:border-{{ $powerColor }}-500/40 transition-colors">
                <div class="text-xs text-gray-400 font-medium mb-1">Current</div>
                <div class="text-2xl md:text-3xl font-bold text-{{ $powerColor }}-400">
                    {{ number_format($d->battery_current ?? 0, 2) }}<span class="text-base font-normal text-gray-400"> A</span>
                </div>
                <div class="mt-2 text-xs text-{{ $powerColor }}-500 font-medium">{{ $powerDir }}</div>
            </div>

            {{-- Power --}}
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-{{ $powerColor }}-500/20 hover:border-{{ $powerColor }}-500/40 transition-colors">
                <div class="text-xs text-gray-400 font-medium mb-1">Power</div>
                <div class="text-2xl md:text-3xl font-bold text-{{ $powerColor }}-400">
                    {{ number_format(abs($d->power ?? 0), 1) }}<span class="text-base font-normal text-gray-400"> W</span>
                </div>
                <div class="mt-2 text-xs text-gray-500">{{ $powerDir }}</div>
            </div>
        </div>

        {{-- ===== ROW 2: TEMP + CYCLE + ALARM ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4 mb-4">
            {{-- Temp Batt 1 --}}
            @php $t1=$d->battery_temperature??0; $tc1=$t1>45?'red':($t1>35?'yellow':'emerald'); @endphp
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-emerald-500/15">
                <div class="text-xs text-gray-400 mb-1">🌡 Batt T1</div>
                <div class="text-2xl font-bold text-{{ $tc1 }}-400">{{ number_format($t1,1) }}<span class="text-sm font-normal text-gray-400"> °C</span></div>
                <div class="mt-1 text-xs text-gray-500">Sensor 1</div>
            </div>

            {{-- Temp Batt 2 --}}
            @php $t2=$d->temperature2??0; $tc2=$t2>45?'red':($t2>35?'yellow':'cyan'); @endphp
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-cyan-500/15">
                <div class="text-xs text-gray-400 mb-1">🌡 Batt T2</div>
                <div class="text-2xl font-bold text-{{ $tc2 }}-400">{{ number_format($t2,1) }}<span class="text-sm font-normal text-gray-400"> °C</span></div>
                <div class="mt-1 text-xs text-gray-500">Sensor 2</div>
            </div>

            {{-- MOS Temp (dari ESP32 register 0x12A2) --}}
            @php $tm=$d->mos_temp??0; $tcm=$tm>65?'red':($tm>50?'yellow':'orange'); @endphp
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-orange-500/15">
                <div class="text-xs text-gray-400 mb-1">🔥 MOS Temp</div>
                <div class="text-2xl font-bold text-{{ $tcm }}-400">{{ number_format($tm,1) }}<span class="text-sm font-normal text-gray-400"> °C</span></div>
                <div class="mt-1 text-xs text-gray-500">MOSFET</div>
            </div>

            {{-- Cycle Count --}}
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-purple-500/30">
                <div class="text-xs text-gray-400 mb-1">🔄 Cycle</div>
                <div class="text-2xl font-bold text-purple-400">{{ number_format($d->cycle_count??0) }}<span class="text-sm font-normal text-gray-400"> x</span></div>
                @if($d->total_cycle_capacity)
                <div class="mt-1 text-xs text-gray-500">{{ number_format($d->total_cycle_capacity,1) }} Ah</div>
                @endif
            </div>
        </div>

        {{-- ===== ROW 3: BALANCE + MOSFET + ALARM + DIFF ===== --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 md:gap-4 mb-6">
            {{-- Balance --}}
            @php $bc=$d->is_balancing??false; $bcc=$bc?'blue':'gray'; @endphp
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-blue-500/20">
                <div class="text-xs text-gray-400 mb-1">⚖ Balance</div>
                <div class="text-xl font-bold text-{{ $bcc }}-400">{{ number_format($d->balance_current??0,3) }} <span class="text-sm text-gray-400">A</span></div>
                <div class="mt-1 text-xs text-{{ $bcc }}-500">{{ $bc?'● Balancing':'○ Idle' }}</div>
            </div>

            {{-- MOSFET (fix: pakai bit decode, Charge OFF = bit0, Discharge ON = bit1) --}}
            @php
                $mosS = intval($d->mosfet_status ?? 0);
                $chgOn = ($mosS & 0x01) != 0;
                $disOn = ($mosS & 0x02) != 0;
                $mosLabel = match(true) {
                    !$chgOn && !$disOn => 'OFF',
                    $chgOn  && !$disOn => 'Chg Only',
                    !$chgOn &&  $disOn => 'Disch Only',
                    default            => 'Chg+Disch',
                };
                $mosColor = ($mosS === 0) ? 'red' : 'indigo';
            @endphp
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-indigo-500/20">
                <div class="text-xs text-gray-400 mb-1">⚡ MOSFET</div>
                <div class="text-xl font-bold text-{{ $mosColor }}-400">{{ $mosLabel }}</div>
                <div class="mt-1 text-xs text-gray-500">
                    <span class="{{ $chgOn ? 'text-emerald-400' : 'text-red-400/60' }}">CHG {{ $chgOn ? 'ON' : 'OFF' }}</span>
                    <span class="mx-1 text-gray-600">|</span>
                    <span class="{{ $disOn ? 'text-emerald-400' : 'text-red-400/60' }}">DISCH {{ $disOn ? 'ON' : 'OFF' }}</span>
                </div>
            </div>

            {{-- Alarm (gunakan alarm_is_real dari ESP32) --}}
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-{{ $alarmIsReal ? 'red' : 'emerald' }}-500/30">
                <div class="text-xs text-gray-400 mb-1">{{ $alarmIsReal ? '🚨' : '✅' }} Alarm</div>
                <div class="text-xl font-bold text-{{ $alarmIsReal ? 'red' : 'emerald' }}-400">
                    {{ $alarmIsReal ? 'ALARM!' : 'OK' }}
                </div>
                <div class="mt-1 text-[10px] text-{{ $alarmIsReal ? 'red' : 'gray' }}-{{ $alarmIsReal ? '400' : '500' }} leading-snug truncate"
                     title="{{ $alarmText }}">
                    {{ Str::limit($alarmText, 22) }}
                </div>
            </div>

            {{-- Cell Diff --}}
            @php $diffMv=$d->cell_diff_mv??0; $dc=$diffMv>50?'red':($diffMv>20?'yellow':'emerald'); @endphp
            <div class="bg-gray-800/60 rounded-2xl p-4 border border-{{ $dc }}-500/20">
                <div class="text-xs text-gray-400 mb-1">△ Cell Diff</div>
                <div class="text-xl font-bold text-{{ $dc }}-400">{{ $diffMv }} <span class="text-sm text-gray-400">mV</span></div>
                @if(count($cells))
                <div class="mt-1 text-xs text-gray-500">
                    Avg: {{ number_format(array_sum($cells)/max(count($cells),1), 3) }} V
                </div>
                @endif
            </div>
        </div>

        {{-- ======================== REALTIME GRAFIK ======================== --}}
        <div class="mb-6 bg-gray-800/40 rounded-2xl border border-gray-700/50 p-4 md:p-5">
            {{-- Chart header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                    <h3 class="text-sm font-bold text-white">Grafik Realtime</h3>
                    <span id="chart-pts" class="text-xs text-gray-500"></span>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    {{-- Range --}}
                    <div class="flex bg-gray-900/70 rounded-lg p-1 gap-1 border border-gray-700/60">
                        <button onclick="BmsChart.setHours(1)"  id="btn-h1"  class="chart-range-btn active-range">1j</button>
                        <button onclick="BmsChart.setHours(3)"  id="btn-h3"  class="chart-range-btn">3j</button>
                        <button onclick="BmsChart.setHours(6)"  id="btn-h6"  class="chart-range-btn">6j</button>
                        <button onclick="BmsChart.setHours(12)" id="btn-h12" class="chart-range-btn">12j</button>
                        <button onclick="BmsChart.setHours(24)" id="btn-h24" class="chart-range-btn">24j</button>
                    </div>
                    {{-- Spinner --}}
                    <svg id="chart-spinner" class="w-4 h-4 text-emerald-400 hidden" style="animation:spin 1s linear infinite" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                        <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" class="opacity-75"></path>
                    </svg>
                </div>
            </div>

            {{-- Tab buttons --}}
            <div class="flex gap-1.5 flex-wrap mb-4" id="chart-tabs">
                <button onclick="BmsChart.setTab('soc')"     data-tab="soc"     class="chart-tab-btn tab-active">SOC %</button>
                <button onclick="BmsChart.setTab('voltage')" data-tab="voltage" class="chart-tab-btn">Voltage V</button>
                <button onclick="BmsChart.setTab('current')" data-tab="current" class="chart-tab-btn">Current A</button>
                <button onclick="BmsChart.setTab('power')"   data-tab="power"   class="chart-tab-btn">Power W</button>
                <button onclick="BmsChart.setTab('temp')"    data-tab="temp"    class="chart-tab-btn">Suhu °C</button>
            </div>

            {{-- Single Canvas — always visible so Chart.js can measure it --}}
            <div style="position:relative; height:260px;">
                <canvas id="bms-chart"></canvas>
                <div id="chart-empty" class="hidden absolute inset-0 flex items-center justify-center text-gray-500 text-sm">
                    Belum ada data untuk rentang ini.
                </div>
            </div>
        </div>

        {{-- ===== CELL VOLTAGES ===== --}}
        @if($cellCount > 0)
        @php
            $vmin = count($cells) ? min($cells) : 0;
            $vmax = count($cells) ? max($cells) : 0;
            $vavg = count($cells) ? array_sum($cells)/count($cells) : 0;
        @endphp
        <div class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-purple-400 uppercase tracking-wider">🔋 Cell Voltages ({{ $cellCount }} sel)</h3>
                <div class="flex gap-3 text-xs text-gray-400">
                    <span>Min: <b class="text-red-400">{{ number_format($vmin,3) }}V</b></span>
                    <span>Avg: <b class="text-blue-400">{{ number_format($vavg,3) }}V</b></span>
                    <span>Max: <b class="text-emerald-400">{{ number_format($vmax,3) }}V</b></span>
                </div>
            </div>
            <div class="bg-gray-800/50 rounded-2xl p-4 border border-purple-500/20">
                <div class="grid grid-cols-4 sm:grid-cols-8 lg:grid-cols-16 gap-1.5">
                    @for($i = 0; $i < $cellCount; $i++)
                        @php
                            $v     = $cells[$i] ?? 0;
                            $isMin = ($vmax > $vmin && $v == $vmin);
                            $isMax = ($vmax > $vmin && $v == $vmax);
                            $bar   = $vmax > $vmin ? (($v-$vmin)/($vmax-$vmin))*100 : 50;
                            if ($v >= 3.55)     $cc = 'text-emerald-400 border-emerald-500/40 bg-emerald-500/5';
                            elseif ($v >= 3.40) $cc = 'text-cyan-400    border-cyan-500/30    bg-cyan-500/5';
                            elseif ($v >= 3.20) $cc = 'text-yellow-400  border-yellow-500/30  bg-yellow-500/5';
                            elseif ($v > 0)     $cc = 'text-red-400     border-red-500/40     bg-red-500/5';
                            else                $cc = 'text-gray-600    border-gray-700       bg-gray-900/30';
                            $ring = $isMin ? 'ring-1 ring-red-500/70' : ($isMax ? 'ring-1 ring-emerald-500/70' : '');
                        @endphp
                        <div class="rounded-lg p-1.5 border {{ $cc }} {{ $ring }} text-center relative overflow-hidden">
                            <div class="absolute bottom-0 left-0 h-0.5 bg-current opacity-50" style="width:{{ round($bar) }}%"></div>
                            <div class="text-[9px] text-gray-500">C{{ $i+1 }}</div>
                            <div class="text-[11px] font-bold">{{ number_format($v,3) }}</div>
                            @if($isMin)<div class="text-[8px] text-red-400">LOW</div>
                            @elseif($isMax)<div class="text-[8px] text-emerald-400">HIGH</div>
                            @else<div class="invisible text-[8px]">-</div>@endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
        @endif

        {{-- ===== CELL RESISTANCE ===== --}}
        @if(count($resistances) > 0)
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-amber-400 uppercase tracking-wider mb-3">⚙ Cell Resistance (mΩ)</h3>
            <div class="bg-gray-800/50 rounded-2xl p-4 border border-amber-500/20">
                <div class="grid grid-cols-4 sm:grid-cols-8 lg:grid-cols-16 gap-1.5">
                    @foreach($resistances as $idx => $res)
                        @php $rc = $res > 0.5 ? 'red' : ($res > 0.3 ? 'yellow' : 'emerald'); @endphp
                        <div class="bg-gray-900/60 rounded-lg p-2 border border-{{ $rc }}-500/20 text-center">
                            <div class="text-[9px] text-gray-500">C{{ $idx+1 }}</div>
                            <div class="text-[11px] font-bold text-{{ $rc }}-400">{{ number_format($res,3) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @else
        {{-- OFFLINE --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-20 h-20 rounded-full bg-gray-800 border-2 border-red-500/30 flex items-center justify-center mb-5">
                <svg class="w-10 h-10 text-red-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            @if($lastUpdateTime)
                <div class="text-red-400 text-xl font-bold mb-2">⚠️ BMS Offline</div>
                <div class="text-gray-400 text-sm mb-1">Last seen: {{ $lastUpdateTime->format('d M Y H:i:s') }}</div>
                <div class="text-gray-600 text-xs">{{ $lastUpdateTime->diffForHumans() }}</div>
            @else
                <div class="text-gray-400 text-xl font-semibold mb-2">Menunggu Data...</div>
                <div class="text-gray-600 text-sm">ESP32 belum mengirim data.</div>
            @endif
        </div>
        @endif

    </div>{{-- /main --}}

    {{-- ========================= STYLES ========================= --}}
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }

        .chart-range-btn {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            transition: all .2s;
            cursor: pointer;
            background: transparent;
            border: none;
        }
        .chart-range-btn:hover  { color: #d1d5db; }
        .chart-range-btn.active-range {
            background: rgba(52,211,153,0.2);
            border: 1px solid rgba(52,211,153,0.4);
            color: #34d399;
        }
        .chart-tab-btn {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            border: 1px solid rgba(255,255,255,0.07);
            background: transparent;
            cursor: pointer;
            transition: all .2s;
        }
        .chart-tab-btn:hover { color: #d1d5db; border-color: rgba(255,255,255,0.15); }
        .chart-tab-btn.tab-active {
            background: rgba(52,211,153,0.15);
            border-color: rgba(52,211,153,0.4);
            color: #34d399;
        }
    </style>

    {{-- ========================= SCRIPTS ========================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
    /* ================================================================
       BmsChart — single Chart instance, swap datasets on tab change
    ================================================================ */
    (function(){
        'use strict';

        Chart.defaults.color       = '#9ca3af';
        Chart.defaults.borderColor = 'rgba(255,255,255,0.05)';

        // Palette per tab
        const TABS = {
            soc:     { label:['SOC (%)'],            color:['#34d399'], fill:[true] },
            voltage: { label:['Voltage (V)'],         color:['#22d3ee'], fill:[true] },
            current: { label:['Current (A)'],         color:['#fb923c'], fill:[false] },
            power:   { label:['Power (W)'],           color:['#facc15'], fill:[true] },
            temp:    { label:['Temp1 (°C)','Temp2 (°C)'], color:['#f87171','#c084fc'], fill:[false,false] },
        };

        // State
        let chart        = null;
        let activeTab    = 'soc';
        let activeHours  = 1;
        let latestData   = {};
        let timer        = null;

        function hex2rgba(hex, a){ const r=parseInt(hex.slice(1,3),16),g=parseInt(hex.slice(3,5),16),b=parseInt(hex.slice(5,7),16); return`rgba(${r},${g},${b},${a})`; }

        function makeDatasets(tab){
            const t = TABS[tab];
            return t.label.map((lbl,i)=>({
                label:           lbl,
                data:            [],
                borderColor:     t.color[i],
                backgroundColor: t.fill[i] ? hex2rgba(t.color[i],0.12) : 'transparent',
                borderWidth:     2,
                pointRadius:     0,
                pointHoverRadius:4,
                tension:         0.35,
                fill:            t.fill[i],
            }));
        }

        function buildChart(){
            const ctx = document.getElementById('bms-chart');
            if (!ctx){ return; }
            if (chart){ chart.destroy(); }

            chart = new Chart(ctx, {
                type: 'line',
                data: { labels:[], datasets: makeDatasets(activeTab) },
                options:{
                    animation:  { duration:400 },
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction:{ mode:'index', intersect:false },
                    plugins:{
                        legend:{
                            display: activeTab === 'temp',
                            labels:{ boxWidth:12, padding:12, font:{size:11} }
                        },
                        tooltip:{
                            backgroundColor:'rgba(10,15,30,0.95)',
                            borderColor:'rgba(255,255,255,0.08)',
                            borderWidth:1,
                            padding:10,
                            titleFont:{size:11},
                            bodyFont:{size:12},
                        }
                    },
                    scales:{
                        x:{
                            ticks:{ maxTicksLimit:8, font:{size:10}, maxRotation:0 },
                            grid: { color:'rgba(255,255,255,0.04)' }
                        },
                        y:{
                            ticks:{ font:{size:10} },
                            grid: { color:'rgba(255,255,255,0.04)' }
                        }
                    }
                }
            });
        }

        function applyData(){
            if(!chart || !latestData.labels) return;
            const lbl = latestData.labels || [];
            const sets = {
                soc:     [latestData.soc],
                voltage: [latestData.voltage],
                current: [latestData.current],
                power:   [latestData.power],
                temp:    [latestData.temp1, latestData.temp2],
            };
            const series = sets[activeTab] || [];

            // If number of datasets changed (e.g. temp has 2), rebuild
            if (series.length !== chart.data.datasets.length){
                chart.data.datasets = makeDatasets(activeTab);
                chart.options.plugins.legend.display = (activeTab === 'temp');
            }

            chart.data.labels = lbl;
            series.forEach((vals,i)=>{
                if(chart.data.datasets[i]) chart.data.datasets[i].data = vals || [];
            });
            chart.update('none');

            // Update point count
            const el = document.getElementById('chart-pts');
            if(el) el.textContent = lbl.length ? `(${lbl.length} titik)` : '';

            const empty = document.getElementById('chart-empty');
            if(empty) empty.classList.toggle('hidden', lbl.length > 0);
        }

        async function fetchChart(hours){
            setSpinner(true);
            try{
                const r  = await fetch(`/api/monitor/chart?hours=${hours}`);
                const j  = await r.json();
                latestData = j.success ? j : {};
                applyData();
            }catch(e){
                console.warn('[BmsChart] fetch error', e);
            }finally{
                setSpinner(false);
            }
        }

        function setSpinner(on){
            const s = document.getElementById('chart-spinner');
            if(s) s.classList.toggle('hidden', !on);
        }

        function setHours(h){
            activeHours = h;
            // Update range button styles
            [1,3,6,12,24].forEach(n=>{
                const b = document.getElementById(`btn-h${n}`);
                if(b) b.classList.toggle('active-range', n === h);
            });
            fetchChart(h);
        }

        function setTab(tab){
            activeTab = tab;
            // Update tab button styles
            document.querySelectorAll('.chart-tab-btn').forEach(b=>{
                b.classList.toggle('tab-active', b.dataset.tab === tab);
            });
            // Rebuild & re-apply (same data, different series)
            buildChart();
            applyData();
        }

        function startAutoRefresh(){
            if(timer) clearInterval(timer);
            timer = setInterval(()=> fetchChart(activeHours), 8000);
        }

        // Bootstrap
        window.BmsChart = { setHours, setTab };

        // Wait for Chart.js + DOM ready
        document.addEventListener('DOMContentLoaded', ()=>{
            buildChart();
            fetchChart(activeHours);
            startAutoRefresh();
        });

        // Restart after Livewire navigation
        document.addEventListener('livewire:navigated', ()=>{
            buildChart();
            fetchChart(activeHours);
            startAutoRefresh();
        });
    })();

    // Sidebar toggle
    function toggleSidebar(){
        const ov=document.getElementById('sidebar-overlay');
        const p=document.getElementById('sidebar-panel');
        const open=p.classList.contains('sidebar-open');
        p.classList.toggle('sidebar-open',!open);
        p.classList.toggle('sidebar-closed',open);
        ov.classList.toggle('hidden',open);
    }
    </script>
</div>
