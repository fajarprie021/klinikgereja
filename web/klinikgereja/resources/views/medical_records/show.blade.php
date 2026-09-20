<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Rekam Medis #{{ $medicalRecord->id }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-gray-200">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Informasi Pasien</h3>
                    <p class="text-lg font-bold text-gray-900 mt-1">{{ $medicalRecord->patient->name }}</p>
                    <p class="text-sm text-gray-600">No. RM: {{ $medicalRecord->patient->patient_number }}</p>
                    <p class="text-sm text-gray-600">Tipe: <span class="font-semibold">{{ $medicalRecord->patient->patient_type }}</span></p>
                    @if ($medicalRecord->patient->church_member_number)
                        <p class="text-sm text-gray-600">No. Anggota Gereja: {{ $medicalRecord->patient->church_member_number }}</p>
                    @endif
                    <p class="text-sm text-gray-600">No. Telp: {{ $medicalRecord->patient->phone ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Informasi Pemeriksaan</h3>
                    <p class="text-sm text-gray-600 mt-1">Tanggal: <strong>{{ $medicalRecord->created_at->format('d M Y, H:i') }}</strong></p>
                    <p class="text-sm text-gray-600">Dokter: <strong>{{ $medicalRecord->doctor->name }}</strong> ({{ $medicalRecord->doctor->specialization }})</p>
                    @if ($medicalRecord->appointment)
                        <p class="text-sm text-gray-600">No. Antrean: <strong>#{{ $medicalRecord->appointment->queue_number }}</strong> (Status: {{ $medicalRecord->appointment->status }})</p>
                    @endif
                </div>
            </div>

            <!-- Tanda Vital -->
            <div class="pb-4 border-b border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-3">Tanda-Tanda Vital (Vital Signs)</h3>
                @if ($medicalRecord->vitalSign)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-md text-sm">
                        <div><span class="text-gray-500">Tekanan Darah:</span> <strong class="text-gray-800">{{ $medicalRecord->vitalSign->blood_pressure ?? '-' }}</strong></div>
                        <div><span class="text-gray-500">Suhu Tubuh:</span> <strong class="text-gray-800">{{ $medicalRecord->vitalSign->temperature ? $medicalRecord->vitalSign->temperature.' °C' : '-' }}</strong></div>
                        <div><span class="text-gray-500">Berat Badan:</span> <strong class="text-gray-800">{{ $medicalRecord->vitalSign->weight ? $medicalRecord->vitalSign->weight.' kg' : '-' }}</strong></div>
                        <div><span class="text-gray-500">Tinggi Badan:</span> <strong class="text-gray-800">{{ $medicalRecord->vitalSign->height ? $medicalRecord->vitalSign->height.' cm' : '-' }}</strong></div>
                        <div><span class="text-gray-500">Detak Nadi:</span> <strong class="text-gray-800">{{ $medicalRecord->vitalSign->heart_rate ? $medicalRecord->vitalSign->heart_rate.' bpm' : '-' }}</strong></div>
                        <div><span class="text-gray-500">Pernapasan:</span> <strong class="text-gray-800">{{ $medicalRecord->vitalSign->respiratory_rate ? $medicalRecord->vitalSign->respiratory_rate.' /mnt' : '-' }}</strong></div>
                    </div>
                    @if ($medicalRecord->vitalSign->notes)
                        <p class="text-xs text-gray-500 mt-2">Catatan Vital: {{ $medicalRecord->vitalSign->notes }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500 italic">Belum ada data tanda vital yang dicatat.</p>
                @endif
            </div>

            <!-- Hasil Pemeriksaan Medis -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Keluhan (Anamnesis)</h3>
                    <p class="text-gray-900 mt-1 whitespace-pre-line">{{ $medicalRecord->complaint }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Diagnosis</h3>
                    <p class="text-gray-900 mt-1 whitespace-pre-line">{{ $medicalRecord->diagnosis ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Tindakan / Terapi / Obat</h3>
                    <p class="text-gray-900 mt-1 whitespace-pre-line">{{ $medicalRecord->treatment ?? '-' }}</p>
                </div>
                @if ($medicalRecord->notes)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Catatan Tambahan</h3>
                        <p class="text-gray-900 mt-1 whitespace-pre-line">{{ $medicalRecord->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-200 flex items-center gap-4">
                <a href="{{ route('medical-records.index') }}" class="text-gray-600 hover:underline text-sm">Kembali ke Daftar</a>
                @can('update', $medicalRecord)
                    <a href="{{ route('medical-records.edit', $medicalRecord) }}" class="bg-yellow-500 text-white px-3 py-1.5 rounded-md text-sm hover:bg-yellow-600">Edit Rekam Medis</a>
                @endcan
                @can('delete', $medicalRecord)
                    <form method="POST" action="{{ route('medical-records.destroy', $medicalRecord) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rekam medis ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-red-700">Hapus</button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
