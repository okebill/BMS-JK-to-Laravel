<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#111827">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>{{ $title ?? 'Realtime Okenet BMS Monitoring' }}</title>

    @livewireStyles
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    screens: { 'xs': '480px' }
                }
            }
        }
    </script>
    <style>
        html, body { overflow-x: hidden; -webkit-tap-highlight-color: transparent; }
        * { -webkit-overflow-scrolling: touch; }
        .sidebar-overlay { transition: opacity 0.3s ease; }
        .sidebar-panel   { transition: transform 0.3s ease; }
        .sidebar-panel.sidebar-closed { transform: translateX(-100%); }
        .sidebar-panel.sidebar-open   { transform: translateX(0); }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body>
    {{ $slot }}

    {{-- Livewire 3 bundles Alpine.js — jangan import Alpine CDN terpisah --}}
    @livewireScripts
    @stack('scripts')
</body>
</html>

