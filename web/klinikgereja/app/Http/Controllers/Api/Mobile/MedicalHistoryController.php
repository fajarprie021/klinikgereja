<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalHistoryController extends Controller
{
    /**
     * Get medical records history for the authenticated patient.
     */
    public function index(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'message' => 'Profil pasien tidak ditemukan.',
            ], 422);
        }

        $records = MedicalRecord::with(['doctor', 'vitalSign', 'appointment'])
            ->where('patient_id', $patient->id)
            ->latest('id')
            ->get();

        return response()->json([
            'data' => $records,
        ]);
    }

    /**
     * Get detail of a specific medical record.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $patient = $request->user()->patient;

        if (!$patient) {
            return response()->json([
                'message' => 'Profil pasien tidak ditemukan.',
            ], 422);
        }

        $record = MedicalRecord::with(['doctor', 'vitalSign', 'appointment'])
            ->where('patient_id', $patient->id)
            ->where('id', $id)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'Catatan rekam medis tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $record,
        ]);
    }
}
