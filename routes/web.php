<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadMonitoringController;
use App\Http\Controllers\MonitoringController;

Route::get('/', [MonitoringController::class, 'dashboard'])
    ->name('dashboard');

Route::get('/dashboard', [MonitoringController::class, 'dashboard'])
    ->name('dashboard');

Route::get('/upload', [MonitoringController::class, 'dashboard'])
    ->name('upload');

Route::post('/monitoring/upload', [UploadMonitoringController::class, 'upload'])
    ->name('monitoring.upload');

Route::get('/monitoring/pml/{id}', [MonitoringController::class, 'getDefaultData'])
    ->name('monitoring.perpml');
    
Route::get('/monitoring/pml/{id}/chart', [MonitoringController::class, 'filterTanggalPML'])
    ->name('monitoring.chart');

Route::get('/monitoring/pml/{id}/chartMingguan', [MonitoringController::class, 'filterMingguan'])
    ->name('monitoring.chartMingguan');