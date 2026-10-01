<?php

use App\Http\Controllers\Api\AttendanceApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| RESTful API Routes (Master Siswa & Data Absen)
|--------------------------------------------------------------------------
*/

// Master Data Siswa API
Route::get('/students', [StudentApiController::class, 'index']);
Route::post('/students', [StudentApiController::class, 'store']);
Route::get('/students/{student}', [StudentApiController::class, 'show']);
Route::put('/students/{student}', [StudentApiController::class, 'update']);
Route::delete('/students/{student}', [StudentApiController::class, 'destroy']);

// Data Absensi API
Route::get('/attendances', [AttendanceApiController::class, 'index']);
Route::post('/attendances', [AttendanceApiController::class, 'store']);
Route::get('/attendances/status', [AttendanceApiController::class, 'status']);
Route::get('/attendances/summary', [AttendanceApiController::class, 'summary']);
Route::delete('/attendances/{attendance}', [AttendanceApiController::class, 'destroy']);

// Autocomplete & Suggestions API
Route::get('/suggestions', [AttendanceController::class, 'suggestions']);
