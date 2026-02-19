<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white" wire:poll.3s="loadLogs">
    <!-- Mobile Top Bar -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-gray-900/95 backdrop-blur-lg border-b border-cyan-500/20 px-4 py-3 flex items-center justify-between">
        <button onclick="toggleSidebar()" class="p-2 rounded-lg bg-gray-800/80 border border-cyan-500/30 text-cyan-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div class="flex items-center gap-2">
            <div class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></div>
            <span class="text-sm font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">Serial Monitor</span>
        </div>
        <button wire:click="clearLogs" class="p-2 rounded-lg bg-red-500/20 border border-red-500/30 text-red-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
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
                <a href="{{ route('control') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800/50 text-gray-300 hover:text-cyan-400 transition">
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
                <a href="{{ route('serial-monitor') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-cyan-500/20 border border-cyan-500/30 text-cyan-400">
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
    <div class="lg:ml-64 p-3 md:p-4 lg:p-8 pt-16 lg:pt-8">
        <!-- Header (Desktop only - mobile has top bar) -->
        <div class="hidden lg:flex mb-6 flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">Virtual Serial Monitor</h2>
                <p class="text-gray-400 text-sm md:text-base">Remote debugging untuk ESP32</p>
            </div>
            
            <!-- Status & Controls -->
            <div class="flex items-center gap-4">
                <!-- Live Indicator -->
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse shadow-lg shadow-green-400/50"></div>
                    <span class="text-sm font-medium text-green-400">LIVE</span>
                </div>
                
                <!-- Clear Button -->
                <button wire:click="clearLogs" 
                    class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/50 rounded-lg text-red-400 font-medium transition text-sm">
                    Clear Terminal
                </button>
            </div>
        </div>

        <!-- Terminal Box -->
        <div class="bg-[#0d1117] rounded-xl border-2 border-cyan-500/30 shadow-2xl shadow-cyan-500/20 overflow-hidden">
            <!-- Terminal Header -->
            <div class="bg-gray-800/50 px-3 md:px-4 py-2 border-b border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-1.5 md:gap-2">
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 bg-red-500 rounded-full"></div>
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 bg-yellow-500 rounded-full"></div>
                    <div class="w-2.5 h-2.5 md:w-3 md:h-3 bg-green-500 rounded-full"></div>
                    <span class="ml-2 md:ml-4 text-gray-400 text-xs md:text-sm font-mono">ESP32 Serial Monitor</span>
                </div>
                <div class="text-[10px] md:text-xs text-gray-500 font-mono">
                    Auto-refresh: 3s
                </div>
            </div>
            
            <!-- Terminal Content -->
            <div class="p-2 md:p-4 h-[calc(100vh-140px)] lg:h-[calc(100vh-250px)] overflow-y-auto font-mono text-[11px] md:text-sm" id="terminal-content">
                @if(count($logs) > 0)
                    @foreach($logs as $log)
                        <div class="mb-1 flex flex-col sm:flex-row sm:items-start gap-0.5 sm:gap-3 hover:bg-gray-900/30 px-1.5 md:px-2 py-0.5 md:py-1 rounded">
                            <!-- Timestamp & Level -->
                            <div class="flex items-center gap-1.5 md:gap-2 flex-shrink-0">
                                <span class="text-cyan-400/70 text-[10px] md:text-xs whitespace-nowrap">
                                    [{{ $log['date'] }} {{ $log['time'] }}]
                                </span>
                                
                                <!-- Level Badge -->
                                @php
                                    $levelColors = [
                                        'info' => 'text-blue-400',
                                        'error' => 'text-red-400',
                                        'warning' => 'text-yellow-400',
                                        'debug' => 'text-gray-400',
                                    ];
                                    $levelBg = [
                                        'info' => 'bg-blue-500/20 border-blue-500/50',
                                        'error' => 'bg-red-500/20 border-red-500/50',
                                        'warning' => 'bg-yellow-500/20 border-yellow-500/50',
                                        'debug' => 'bg-gray-500/20 border-gray-500/50',
                                    ];
                                    $color = $levelColors[$log['level']] ?? 'text-gray-400';
                                    $bg = $levelBg[$log['level']] ?? 'bg-gray-500/20 border-gray-500/50';
                                @endphp
                                <span class="px-1.5 md:px-2 py-0.5 rounded text-[10px] md:text-xs font-semibold {{ $bg }} border {{ $color }}">
                                    {{ strtoupper($log['level']) }}
                                </span>
                            </div>
                            
                            <!-- Message -->
                            <span class="text-green-400 flex-1 break-all md:break-words text-[11px] md:text-sm" style="text-shadow: 0 0 10px rgba(34, 197, 94, 0.5);">
                                {{ $log['message'] }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-gray-500 py-8 md:py-12">
                        <div class="mb-3 md:mb-4">
                            <svg class="w-12 h-12 md:w-16 md:h-16 mx-auto text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-base md:text-lg mb-2">No logs available</p>
                        <p class="text-xs md:text-sm">Waiting for ESP32 to send logs...</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Info Box -->
        <div class="mt-3 md:mt-4 bg-gray-800/30 rounded-lg p-3 md:p-4 border border-cyan-500/20">
            <div class="flex items-start gap-2 md:gap-3">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-cyan-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-xs md:text-sm text-gray-300">
                    <p class="font-semibold text-cyan-400 mb-1">Virtual Serial Monitor</p>
                    <p class="text-gray-400 text-xs md:text-sm">
                        Logs dari ESP32 akan muncul di sini secara real-time. Pastikan ESP32 sudah dikonfigurasi dengan fungsi <code class="bg-gray-900 px-1 rounded text-cyan-400 text-[10px] md:text-xs">webLog()</code> dan mengirim ke endpoint <code class="bg-gray-900 px-1 rounded text-cyan-400 text-[10px] md:text-xs">/api/monitor/log</code>.
                    </p>
                </div>
            </div>
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

    @push('styles')
    <style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap');

    .font-mono {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
    }

    #terminal-content {
        scrollbar-width: thin;
        scrollbar-color: rgba(34, 211, 238, 0.3) transparent;
    }

    #terminal-content::-webkit-scrollbar {
        width: 6px;
    }

    #terminal-content::-webkit-scrollbar-track {
        background: transparent;
    }

    #terminal-content::-webkit-scrollbar-thumb {
        background: rgba(34, 211, 238, 0.3);
        border-radius: 4px;
    }

    #terminal-content::-webkit-scrollbar-thumb:hover {
        background: rgba(34, 211, 238, 0.5);
    }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Auto scroll to bottom when new logs arrive
        Livewire.on('logs-updated', () => {
            const terminal = document.getElementById('terminal-content');
            if (terminal) {
                terminal.scrollTop = terminal.scrollHeight;
            }
        });

        // Scroll to bottom on mount and after polling
        document.addEventListener('DOMContentLoaded', () => {
            const terminal = document.getElementById('terminal-content');
            if (terminal) {
                terminal.scrollTop = terminal.scrollHeight;
            }
        });

        // Auto scroll after Livewire updates
        setInterval(() => {
            const terminal = document.getElementById('terminal-content');
            if (terminal) {
                terminal.scrollTop = terminal.scrollHeight;
            }
        }, 3200); // Slightly after 3s poll
    </script>
    @endpush
</div>
