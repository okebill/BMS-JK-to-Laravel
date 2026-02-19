<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/control', \App\Livewire\ControlPanel::class)->name('control');
    Route::get('/bms-settings', \App\Livewire\BmsFullSettings::class)->name('bms-settings');
    Route::get('/serial-monitor', \App\Livewire\RemoteTerminal::class)->name('serial-monitor');
});


