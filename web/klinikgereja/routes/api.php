<?php

use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\BookingController;
use App\Http\Controllers\Api\Mobile\MedicalHistoryController;
use App\Http\Controllers\Api\Mobile\QueueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile REST API Routes (Phase 4)
|--------------------------------------------------------------------------
*/

Route::prefix('v1/mobile')->group(function () {
    // Auth Publik
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Info Dokter & Jadwal (Publik)
    Route::get('doctors', [BookingController::class, 'getDoctors']);
    Route::get('doctors/{doctor}/schedules', [BookingController::class, 'getDoctorSchedules']);

    // Fitur Pasien Terotentikasi (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Booking & Antrean
        Route::post('appointments/book', [BookingController::class, 'bookAppointment']);
        Route::get('appointments/my-queue', [QueueController::class, 'myAppointments']);
        Route::get('doctors/{doctor}/live-queue', [QueueController::class, 'liveQueue']);

        // Riwayat Rekam Medis Pasien
        Route::get('medical-records/my-history', [MedicalHistoryController::class, 'index']);
        Route::get('medical-records/my-history/{id}', [MedicalHistoryController::class, 'show']);
    });
});
