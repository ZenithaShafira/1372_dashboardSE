<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadMonitoringController;
use App\Http\Controllers\MonitoringController;

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/upload', [UploadMonitoringController::class, 'index'])
    ->name('monitoring.upload');
    
Route::post('/upload-monitoring', [UploadMonitoringController::class, 'upload'])
    ->name('monitoring.upload-monitoring');

Route::get('/monitoring/pml/{id}', [MonitoringController::class, 'getDefaultData'])
    ->name('monitoring.perpml');
    
Route::get('/monitoring/pml/{id}/chart', [MonitoringController::class, 'filterTanggalPML'])
    ->name('monitoring.chart');

Route::get('/monitoring/pml/{id}/chartMingguan', [MonitoringController::class, 'filterMingguan'])
    ->name('monitoring.chartMingguan');