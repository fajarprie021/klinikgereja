<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Get list of doctors and their specialties & schedules.
     */
    public function getDoctors(): JsonResponse
    {
        $doctors = Doctor::with('schedules')->orderBy('name')->get();

        return response()->json([
            'data' => $doctors,
        ]);
    }

    /**
     * Get schedules for a specific doctor.
     */
    public function getDoctorSchedules(Doctor $doctor): JsonResponse
    {
        $schedules = $doctor->schedules()->orderBy('day')->get();

        return response()->json([
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialization' => $doctor->specialization,
            ],
            'data' => $schedules,
        ]);
    }

    /**
     * Book a new appointment queue for the authenticated patient.
     */
    public function bookAppointment(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'message' => 'Profil pasien tidak ditemukan untuk akun ini.',
            ], 422);
        }

        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        // Check if already booked with same doctor on same date
        $existing = Appointment::where('patient_id', $patient->id)
            ->where('doctor_id', $validated['doctor_id'])
            ->whereDate('date', $validated['date'])
            ->whereIn('status', ['WAITING', 'CHECKED'])
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah memiliki antrean aktif untuk dokter ini pada tanggal tersebut.',
                'data' => $existing,
            ], 422);
        }

        $appointment = DB::transaction(function () use ($patient, $validated) {
            $queueNumber = ((int) Appointment::where('doctor_id', $validated['doctor_id'])
                ->whereDate('date', $validated['date'])
                ->lockForUpdate()
                ->max('queue_number')) + 1;

            return Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $validated['doctor_id'],
                'date' => $validated['date'],
                'queue_number' => $queueNumber,
                'status' => 'WAITING',
            ]);
        });

        $appointment->load(['doctor', 'patient']);

        return response()->json([
            'message' => 'Booking antrean berhasil.',
            'data' => [
                'id' => $appointment->id,
                'queue_number' => $appointment->queue_number,
                'date' => $appointment->date->format('Y-m-d'),
                'status' => $appointment->status,
                'doctor' => [
                    'name' => $appointment->doctor->name,
                    'specialization' => $appointment->doctor->specialization,
                ],
                'patient' => [
                    'name' => $appointment->patient->name,
                    'patient_number' => $appointment->patient->patient_number,
                ],
            ],
        ], 201);
    }
}
