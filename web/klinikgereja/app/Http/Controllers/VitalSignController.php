<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVitalSignRequest;
use App\Models\MedicalRecord;
use App\Models\VitalSign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class VitalSignController extends Controller
{
    /**
     * Store or update vital sign for a medical record.
     */
    public function store(StoreVitalSignRequest $request): RedirectResponse
    {
        Gate::authorize('create', VitalSign::class);

        $data = $request->validated();

        $vitalSign = VitalSign::updateOrCreate(
            ['medical_record_id' => $data['medical_record_id']],
            [
                'blood_pressure' => $data['blood_pressure'] ?? null,
                'temperature' => $data['temperature'] ?? null,
                'weight' => $data['weight'] ?? null,
                'height' => $data['height'] ?? null,
                'heart_rate' => $data['heart_rate'] ?? null,
                'respiratory_rate' => $data['respiratory_rate'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]
        );

        return redirect()->route('medical-records.show', $vitalSign->medical_record_id)
            ->with('success', 'Tanda vital berhasil disimpan.');
    }
}
