<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengajuan Subsidi Pelayanan Kesehatan
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @if (isset($selectedMedicalRecord))
            <div class="mb-4 bg-blue-50 border border-blue-200 p-4 rounded-md">
                <h4 class="font-semibold text-blue-900">Terkait Rekam Medis:</h4>
                <p class="text-sm text-blue-800">
                    Pasien: <strong>{{ $selectedMedicalRecord->patient->name }}</strong> |
                    Keluhan: {{ Str::limit($selectedMedicalRecord->complaint, 50) }} |
                    Diagnosis: {{ $selectedMedicalRecord->diagnosis ?? '-' }}
                </p>
            </div>
        @endif

        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('subsidies.store') }}" class="space-y-4">
                @csrf

                @if (isset($selectedMedicalRecord))
                    <input type="hidden" name="medical_record_id" value="{{ $selectedMedicalRecord->id }}">
                    <input type="hidden" name="patient_id" value="{{ $selectedMedicalRecord->patient_id }}">
                @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pasien *</label>
                        <select name="patient_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Pilih Pasien --</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->patient_number }} - {{ $patient->name }} ({{ $patient->patient_type }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Pengajuan *</label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nominal Subsidi (Rp) *</label>
                    <input type="number" step="1000" name="amount" required placeholder="Contoh: 50000" value="{{ old('amount') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('amount')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                @if (Auth::user()->hasRole(['Bendahara', 'Super Admin']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Persetujuan</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="APPROVED" {{ old('status') === 'APPROVED' ? 'selected' : '' }}>Langsung Setujui (APPROVED)</option>
                            <option value="PENDING" {{ old('status') === 'PENDING' ? 'selected' : '' }}>Menunggu Persetujuan (PENDING)</option>
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan / Alasan Subsidi</label>
                    <textarea name="notes" rows="3" placeholder="Alasan pemberian bantuan subsidi..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                    @error('notes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Pengajuan</button>
                    <a href="{{ route('subsidies.index') }}" class="text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
