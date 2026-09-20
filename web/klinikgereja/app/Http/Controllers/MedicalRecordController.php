<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\VitalSign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', MedicalRecord::class);

        $query = MedicalRecord::with(['patient', 'doctor', 'vitalSign', 'appointment'])
            ->latest('id');

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('complaint', 'like', "%{$search}%")
                    ->orWhere('diagnosis', 'like', "%{$search}%")
                    ->orWhere('treatment', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('patient_number', 'like', "%{$search}%");
                    });
            });
        }

        $medicalRecords = $query->paginate(20)->withQueryString();

        return view('medical_records.index', compact('medicalRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', MedicalRecord::class);

        $patients = Patient::orderBy('name')->get();
        $doctors = Doctor::orderBy('name')->get();
        $selectedAppointment = null;

        if ($request->filled('appointment_id')) {
            $selectedAppointment = Appointment::with(['patient', 'doctor'])->find($request->appointment_id);
        }

        return view('medical_records.create', compact('patients', 'doctors', 'selectedAppointment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalRecordRequest $request): RedirectResponse
    {
        Gate::authorize('create', MedicalRecord::class);

        $data = $request->validated();

        $medicalRecord = DB::transaction(function () use ($data) {
            $record = MedicalRecord::create([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'appointment_id' => $data['appointment_id'] ?? null,
                'complaint' => $data['complaint'],
                'diagnosis' => $data['diagnosis'] ?? null,
                'treatment' => $data['treatment'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $hasVital = !empty($data['blood_pressure'])
                || !empty($data['temperature'])
                || !empty($data['weight'])
                || !empty($data['height'])
                || !empty($data['heart_rate'])
                || !empty($data['respiratory_rate'])
                || !empty($data['vital_notes']);

            if ($hasVital) {
                VitalSign::create([
                    'medical_record_id' => $record->id,
                    'blood_pressure' => $data['blood_pressure'] ?? null,
                    'temperature' => $data['temperature'] ?? null,
                    'weight' => $data['weight'] ?? null,
                    'height' => $data['height'] ?? null,
                    'heart_rate' => $data['heart_rate'] ?? null,
                    'respiratory_rate' => $data['respiratory_rate'] ?? null,
                    'notes' => $data['vital_notes'] ?? null,
                ]);
            }

            if (!empty($data['appointment_id'])) {
                Appointment::where('id', $data['appointment_id'])
                    ->where('status', '!=', 'CANCELLED')
                    ->update(['status' => 'DONE']);
            }

            return $record;
        });

        return redirect()->route('medical-records.show', $medicalRecord)
            ->with('success', 'Rekam medis berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord): View
    {
        Gate::authorize('view', $medicalRecord);

        $medicalRecord->load(['patient', 'doctor', 'vitalSign', 'appointment']);

        return view('medical_records.show', compact('medicalRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord): View
    {
        Gate::authorize('update', $medicalRecord);

        $medicalRecord->load(['patient', 'doctor', 'vitalSign', 'appointment']);
        $patients = Patient::orderBy('name')->get();
        $doctors = Doctor::orderBy('name')->get();

        return view('medical_records.edit', compact('medicalRecord', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        Gate::authorize('update', $medicalRecord);

        $data = $request->validated();

        DB::transaction(function () use ($medicalRecord, $data) {
            $medicalRecord->update([
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'appointment_id' => $data['appointment_id'] ?? null,
                'complaint' => $data['complaint'],
                'diagnosis' => $data['diagnosis'] ?? null,
                'treatment' => $data['treatment'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $vitalData = [
                'blood_pressure' => $data['blood_pressure'] ?? null,
                'temperature' => $data['temperature'] ?? null,
                'weight' => $data['weight'] ?? null,
                'height' => $data['height'] ?? null,
                'heart_rate' => $data['heart_rate'] ?? null,
                'respiratory_rate' => $data['respiratory_rate'] ?? null,
                'notes' => $data['vital_notes'] ?? null,
            ];

            if ($medicalRecord->vitalSign) {
                $medicalRecord->vitalSign->update($vitalData);
            } else {
                $hasVital = !empty($data['blood_pressure'])
                    || !empty($data['temperature'])
                    || !empty($data['weight'])
                    || !empty($data['height'])
                    || !empty($data['heart_rate'])
                    || !empty($data['respiratory_rate'])
                    || !empty($data['vital_notes']);

                if ($hasVital) {
                    VitalSign::create([
                        'medical_record_id' => $medicalRecord->id,
                        ...$vitalData,
                    ]);
                }
            }
        });

        return redirect()->route('medical-records.show', $medicalRecord)
            ->with('success', 'Rekam medis berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecord $medicalRecord): RedirectResponse
    {
        Gate::authorize('delete', $medicalRecord);

        $medicalRecord->delete();

        return redirect()->route('medical-records.index')
            ->with('success', 'Rekam medis berhasil dihapus.');
    }
}
