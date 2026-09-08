<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('patients', PatientController::class)
        ->middleware('role:Admin');

    Route::resource('doctors', DoctorController::class)
        ->middleware('role:Admin');

    Route::resource('schedules', ScheduleController::class)
        ->middleware('role:Admin');

    Route::resource('appointments', AppointmentController::class)
        ->except(['create', 'store', 'edit', 'update', 'destroy']);
    Route::post('appointments/{appointment}/status/{status}', [AppointmentController::class, 'status'])
        ->name('appointments.status');
    Route::resource('appointments', AppointmentController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy'])
        ->middleware('role:Admin');
});

require __DIR__.'/auth.php';
