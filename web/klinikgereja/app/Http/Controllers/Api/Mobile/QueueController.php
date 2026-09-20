<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    /**
     * Get list of patient's appointments.
     */
    public function myAppointments(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'message' => 'Profil pasien tidak ditemukan.',
            ], 422);
        }

        $appointments = Appointment::with(['doctor', 'medicalRecord.vitalSign'])
            ->where('patient_id', $patient->id)
            ->latest('date')
            ->latest('id')
            ->get();

        return response()->json([
            'data' => $appointments,
        ]);
    }

    /**
     * Get live queue monitoring for a specific doctor on a given date.
     */
    public function liveQueue(Request $request, Doctor $doctor): JsonResponse
    {
        $date = $request->filled('date') ? $request->date : now()->toDateString();

        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', $date)
            ->orderBy('queue_number')
            ->get();

        $totalQueue = $appointments->count();
        $doneCount = $appointments->where('status', 'DONE')->count();
        $currentlyServing = $appointments->where('status', 'CHECKED')->first()
            ?? $appointments->where('status', 'WAITING')->first();

        $myAppointment = null;
        $queueAhead = 0;

        if ($request->user() && $request->user()->patient) {
            $patientId = $request->user()->patient->id;
            $myAppointment = $appointments->where('patient_id', $patientId)->first();

            if ($myAppointment && $myAppointment->status === 'WAITING') {
                $queueAhead = $appointments->where('status', 'WAITING')
                    ->where('queue_number', '<', $myAppointment->queue_number)
                    ->count();
            }
        }

        return response()->json([
            'data' => [
                'doctor' => [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialization' => $doctor->specialization,
                ],
                'date' => $date,
                'total_queue' => $totalQueue,
                'completed_count' => $doneCount,
                'currently_serving_queue' => $currentlyServing?->queue_number ?? 0,
                'currently_serving_status' => $currentlyServing?->status ?? 'IDLE',
                'my_appointment' => $myAppointment ? [
                    'id' => $myAppointment->id,
                    'queue_number' => $myAppointment->queue_number,
                    'status' => $myAppointment->status,
                    'people_ahead' => $queueAhead,
                ] : null,
            ],
        ]);
    }
}
