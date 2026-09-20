<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Tagihan / Transaksi Baru
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @if (isset($selectedMedicalRecord))
            <div class="mb-4 bg-blue-50 border border-blue-200 p-4 rounded-md text-sm">
                <h4 class="font-semibold text-blue-900">Terkait Pemeriksaan:</h4>
                <p class="text-blue-800">
                    Pasien: <strong>{{ $selectedMedicalRecord->patient->name }}</strong> ({{ $selectedMedicalRecord->patient->patient_type }}) |
                    Dokter: {{ $selectedMedicalRecord->doctor->name }} |
                    Diagnosis: {{ $selectedMedicalRecord->diagnosis ?? '-' }}
                </p>
                @if ($suggestedSubsidy > 0)
                    <p class="mt-1 text-emerald-700 font-semibold">Tersedia subsidi kesehatan gereja yang disetujui: Rp {{ number_format($suggestedSubsidy, 0, ',', '.') }}</p>
                @endif
            </div>
        @endif

        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
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
                    <label class="block text-sm font-medium text-gray-700">Biaya Layanan & Obat (Rp) *</label>
                    <input type="number" id="service_cost" name="service_cost" step="1000" required placeholder="Contoh: 75000" value="{{ old('service_cost') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('service_cost')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Potongan Subsidi Gereja (Rp)</label>
                    <input type="number" id="subsidy_amount" name="subsidy_amount" step="1000" placeholder="0" value="{{ old('subsidy_amount', $suggestedSubsidy) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('subsidy_amount')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                    <select name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="CASH">Tunai (Cash)</option>
                        <option value="TRANSFER">Transfer Bank / QRIS</option>
                        <option value="SUBSIDY_FULL">Subsidi Penuh Gereja (Gratis)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="PAID">Langsung Lunas (PAID)</option>
                        <option value="UNPAID">Belum Dibayar (UNPAID)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan Kasir</label>
                    <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Transaksi</button>
                    <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
