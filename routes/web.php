<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AttendanceController::class, 'form'])->name('absen.form');
Route::post('/absen', [AttendanceController::class, 'store'])->name('absen.store')->middleware('throttle:60,1');

Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('absen.dashboard');
Route::get('/status-absen', [AttendanceController::class, 'status'])->name('absen.status');
Route::get('/data-absen', [AttendanceController::class, 'records'])->name('absen.records');
Route::get('/data-absen/download', [AttendanceController::class, 'download'])->name('absen.download');
Route::get('/absen/suggestions', [AttendanceController::class, 'suggestions'])->name('absen.suggestions')->middleware('throttle:60,1');
Route::delete('/data-absen/{attendance}', [AttendanceController::class, 'destroy'])->name('absen.destroy');

// Master Data Student Routes
Route::get('/siswa', [StudentController::class, 'index'])->name('students.index');
Route::post('/siswa', [StudentController::class, 'store'])->name('students.store');
Route::put('/siswa/{student}', [StudentController::class, 'update'])->name('students.update');
Route::delete('/siswa/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
Route::post('/siswa/seed-default', [StudentController::class, 'seedDefault'])->name('students.seed_default');
