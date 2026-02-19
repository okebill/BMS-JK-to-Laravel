<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\LogController;

Route::post('/monitor/store', [MonitorController::class, 'store']);
Route::get('/monitor/latest', [MonitorController::class, 'latest']);
Route::get('/monitor/chart',  [MonitorController::class, 'chart']);

// Log endpoint (no CSRF protection needed)
Route::post('/monitor/log', [LogController::class, 'store']);

// BMS Settings & Commands
Route::get('/bms/settings', [\App\Http\Controllers\Api\BmsCommandController::class, 'getSettings']);
Route::post('/bms/settings', [\App\Http\Controllers\Api\BmsCommandController::class, 'updateSettings']);

// BMS Full Configuration Sync
Route::get('/bms/parameters', [\App\Http\Controllers\Api\BmsFullSettingsController::class, 'getParameters']);
Route::post('/bms/parameters/update', [\App\Http\Controllers\Api\BmsFullSettingsController::class, 'updateParameter']);