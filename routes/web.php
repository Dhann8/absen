<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceController::class, 'form'])->name('absen.form');
Route::post('/absen', [AttendanceController::class, 'store'])->name('absen.store')->middleware('throttle:60,1');

Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('absen.dashboard');
Route::get('/data-absen', [AttendanceController::class, 'records'])->name('absen.records');
Route::get('/data-absen/download', [AttendanceController::class, 'download'])->name('absen.download');
Route::get('/absen/suggestions', [AttendanceController::class, 'suggestions'])->name('absen.suggestions')->middleware('throttle:60,1');
Route::delete('/data-absen/{attendance}', [AttendanceController::class, 'destroy'])->name('absen.destroy');
