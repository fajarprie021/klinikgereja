<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\VitalSignController;
use App\Http\Controllers\SubsidyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ChurchFundController;
use App\Http\Controllers\ReportController;
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

    Route::resource('medical-records', MedicalRecordController::class)
        ->middleware('role:Admin,Dokter,Perawat,Bendahara');
    Route::post('vital-signs', [VitalSignController::class, 'store'])
        ->name('vital-signs.store')
        ->middleware('role:Admin,Dokter,Perawat');

    // Modul Finansial & Kas
    Route::resource('subsidies', SubsidyController::class)
        ->middleware('role:Bendahara,Admin');
    Route::post('subsidies/{subsidy}/approve', [SubsidyController::class, 'approve'])
        ->name('subsidies.approve')
        ->middleware('role:Bendahara');
    Route::post('subsidies/{subsidy}/reject', [SubsidyController::class, 'reject'])
        ->name('subsidies.reject')
        ->middleware('role:Bendahara');

    Route::resource('transactions', TransactionController::class)
        ->middleware('role:Bendahara,Admin');
    Route::post('transactions/{transaction}/pay', [TransactionController::class, 'pay'])
        ->name('transactions.pay')
        ->middleware('role:Bendahara,Admin');

    Route::resource('church-funds', ChurchFundController::class)
        ->except(['show', 'edit', 'update'])
        ->middleware('role:Bendahara');

    Route::get('reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('role:Bendahara,Admin');
});

require __DIR__.'/auth.php';
