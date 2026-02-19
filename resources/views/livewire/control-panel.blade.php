<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white">
    <!-- Mobile Top Bar -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-gray-900/95 backdrop-blur-lg border-b border-cyan-500/20 px-4 py-3 flex items-center justify-between">
        <button onclick="toggleSidebar()" class="p-2 rounded-lg bg-gray-800/80 border border-cyan-500/30 text-cyan-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-lg font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">Control Panel</h1>
        <div class="w-9"></div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay fixed inset-0 bg-black/60 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div id="sidebar-panel" class="sidebar-panel sidebar-closed lg:!transform-none fixed left-0 top-0 h-full w-64 bg-gray-900/95 backdrop-blur-lg border-r border-cyan-500/20 z-50 lg:z-30">
        <div class="p-6">
            <div class="flex items-center justify-between mb-8 lg:justify-start lg:gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
                        BMS Monitor
                    </h1>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg hover:bg-gray-800 text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800/50 text-gray-300 hover:text-cyan-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('control') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-cyan-500/20 border border-cyan-500/30 text-cyan-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Control Panel
                </a>
                <a href="{{ route('bms-settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800/50 text-gray-300 hover:text-cyan-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    BMS Settings
                </a>
                <a href="{{ route('serial-monitor') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800/50 text-gray-300 hover:text-cyan-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Serial Monitor
                </a>
            </nav>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-red-500/20 text-gray-300 hover:text-red-400 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64 p-4 md:p-6 lg:p-8 pt-16 lg:pt-8">
        <!-- Header -->
        <div class="mb-4 md:mb-8">
            <h2 class="text-2xl md:text-3xl font-bold mb-1 md:mb-2">Control Panel</h2>
            <p class="text-gray-400 text-sm md:text-base">Configure Inverter & BMS Settings</p>
        </div>

        <!-- Messages -->
        @if($successMessage)
            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-400 text-sm md:text-base">
                {{ $successMessage }}
            </div>
        @endif

        @if($errorMessage)
            <div class="mb-4 md:mb-6 p-3 md:p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm md:text-base">
                {{ $errorMessage }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 lg:gap-8">
            <!-- Inverter Configuration -->
            <div class="bg-gray-800/50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-cyan-500/20 shadow-lg shadow-cyan-500/10">
                <h3 class="text-lg md:text-xl font-bold mb-4 md:mb-6 text-cyan-400 flex items-center gap-2">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                    Inverter Configuration
                </h3>

                <form wire:submit.prevent="saveInverterConfig" class="space-y-3 md:space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Modbus Address</label>
                        <input type="number" wire:model="inverterModbusAddress" 
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none"
                            min="1" max="247" required>
                        @error('inverterModbusAddress') <span class="text-red-400 text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Max Charging Current (A)</label>
                        <input type="number" wire:model="inverterMaxChargingCurrent" step="0.1"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none"
                            min="0" max="200" required>
                        @error('inverterMaxChargingCurrent') <span class="text-red-400 text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Inverter Mode</label>
                        <select wire:model="inverterMode" 
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none">
                            <option value="auto">Auto</option>
                            <option value="manual">Manual</option>
                            <option value="grid">Grid</option>
                            <option value="off">Off</option>
                        </select>
                        @error('inverterMode') <span class="text-red-400 text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white font-semibold py-2.5 md:py-3 rounded-lg transition shadow-lg shadow-cyan-500/50 text-sm md:text-base">
                        Save Inverter Config
                    </button>
                </form>
            </div>

            <!-- BMS Configuration -->
            <div class="bg-gray-800/50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-purple-500/20 shadow-lg shadow-purple-500/10">
                <h3 class="text-lg md:text-xl font-bold mb-4 md:mb-6 text-purple-400 flex items-center gap-2">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    BMS Configuration
                </h3>

                <form wire:submit.prevent="saveBmsConfig" class="space-y-3 md:space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Max Voltage (V)</label>
                        <input type="number" wire:model="bmsMaxVoltage" step="0.1"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none"
                            min="0" max="100" required>
                        @error('bmsMaxVoltage') <span class="text-red-400 text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Min Voltage (V)</label>
                        <input type="number" wire:model="bmsMinVoltage" step="0.1"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none"
                            min="0" max="100" required>
                        @error('bmsMinVoltage') <span class="text-red-400 text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Max Current (A)</label>
                        <input type="number" wire:model="bmsMaxCurrent" step="0.1"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none"
                            min="0" max="500" required>
                        @error('bmsMaxCurrent') <span class="text-red-400 text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Max Temperature (°C)</label>
                        <input type="number" wire:model="bmsMaxTemperature" step="0.1"
                            class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 outline-none"
                            min="0" max="100" required>
                        @error('bmsMaxTemperature') <span class="text-red-400 text-xs md:text-sm">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold py-2.5 md:py-3 rounded-lg transition shadow-lg shadow-purple-500/50 text-sm md:text-base">
                        Save BMS Config
                    </button>
                </form>
            </div>
        </div>

        <!-- Device ID -->
        <div class="mt-4 md:mt-8 bg-gray-800/50 backdrop-blur-lg rounded-xl p-4 md:p-6 border border-gray-700">
            <label class="block text-sm font-medium text-gray-300 mb-1.5 md:mb-2">Device ID</label>
            <input type="text" wire:model="deviceId" 
                class="w-full bg-gray-900/50 border border-gray-700 rounded-lg px-3 md:px-4 py-2 text-white text-sm md:text-base focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none"
                placeholder="ESP32-001">
            <p class="text-gray-500 text-xs md:text-sm mt-2">Device ID untuk mengidentifikasi ESP32 yang akan menerima command</p>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const overlay = document.getElementById('sidebar-overlay');
            const panel = document.getElementById('sidebar-panel');
            if (panel.classList.contains('sidebar-closed')) {
                panel.classList.remove('sidebar-closed');
                panel.classList.add('sidebar-open');
                overlay.classList.remove('hidden');
            } else {
                panel.classList.remove('sidebar-open');
                panel.classList.add('sidebar-closed');
                overlay.classList.add('hidden');
            }
        }
    </script>
</div>
