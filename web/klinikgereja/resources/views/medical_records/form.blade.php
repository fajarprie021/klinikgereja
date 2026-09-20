@csrf
<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700">Pasien</label>
        <select name="patient_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Pilih Pasien --</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id', $medicalRecord->patient_id ?? ($selectedAppointment->patient_id ?? '')) == $patient->id ? 'selected' : '' }}>
                    {{ $patient->patient_number }} - {{ $patient->name }} ({{ $patient->patient_type }})
                </option>
            @endforeach
        </select>
        @error('patient_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Dokter Pemeriksa</label>
        <select name="doctor_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Pilih Dokter --</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ old('doctor_id', $medicalRecord->doctor_id ?? ($selectedAppointment->doctor_id ?? '')) == $doctor->id ? 'selected' : '' }}>
                    {{ $doctor->name }} ({{ $doctor->specialization }})
                </option>
            @endforeach
        </select>
        @error('doctor_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    @if (isset($selectedAppointment) || !empty($medicalRecord->appointment_id))
        <input type="hidden" name="appointment_id" value="{{ old('appointment_id', $medicalRecord->appointment_id ?? ($selectedAppointment->id ?? '')) }}">
    @endif

    <div class="border-t border-gray-200 pt-4">
        <h3 class="text-md font-semibold text-gray-800 mb-2">Tanda Vital (Pemeriksaan Awal)</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tekanan Darah (mmHg)</label>
                <input type="text" name="blood_pressure" placeholder="Contoh: 120/80" value="{{ old('blood_pressure', $medicalRecord->vitalSign->blood_pressure ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('blood_pressure')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Suhu Tubuh (°C)</label>
                <input type="number" step="0.1" name="temperature" placeholder="Contoh: 36.5" value="{{ old('temperature', $medicalRecord->vitalSign->temperature ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('temperature')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Berat Badan (kg)</label>
                <input type="number" step="0.1" name="weight" placeholder="Contoh: 65" value="{{ old('weight', $medicalRecord->vitalSign->weight ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('weight')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tinggi Badan (cm)</label>
                <input type="number" step="0.1" name="height" placeholder="Contoh: 170" value="{{ old('height', $medicalRecord->vitalSign->height ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('height')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Detak Nadi (bpm)</label>
                <input type="number" name="heart_rate" placeholder="Contoh: 80" value="{{ old('heart_rate', $medicalRecord->vitalSign->heart_rate ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('heart_rate')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Laju Pernapasan (/mnt)</label>
                <input type="number" name="respiratory_rate" placeholder="Contoh: 18" value="{{ old('respiratory_rate', $medicalRecord->vitalSign->respiratory_rate ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('respiratory_rate')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 pt-4">
        <h3 class="text-md font-semibold text-gray-800 mb-2">Pemeriksaan Dokter</h3>
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">Keluhan Pasien (Anamnesis) *</label>
                <textarea name="complaint" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('complaint', $medicalRecord->complaint ?? '') }}</textarea>
                @error('complaint')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Diagnosis</label>
                <textarea name="diagnosis" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('diagnosis', $medicalRecord->diagnosis ?? '') }}</textarea>
                @error('diagnosis')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tindakan / Terapi / Resep Obat</label>
                <textarea name="treatment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('treatment', $medicalRecord->treatment ?? '') }}</textarea>
                @error('treatment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $medicalRecord->notes ?? '') }}</textarea>
                @error('notes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4 pt-4">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Rekam Medis</button>
        <a href="{{ route('medical-records.index') }}" class="text-gray-600 hover:underline">Batal</a>
    </div>
</div>
