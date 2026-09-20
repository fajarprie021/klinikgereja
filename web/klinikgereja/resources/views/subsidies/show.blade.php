<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detail Pengajuan Subsidi #{{ $subsidy->id }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
            <div class="flex justify-between items-start border-b border-gray-200 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $subsidy->patient->name }}</h3>
                    <p class="text-sm text-gray-600">No. Rekam Medis: {{ $subsidy->patient->patient_number }} | Tipe: <strong>{{ $subsidy->patient->patient_type }}</strong></p>
                    @if ($subsidy->patient->church_member_number)
                        <p class="text-sm text-gray-600">No. Anggota Gereja: {{ $subsidy->patient->church_member_number }}</p>
                    @endif
                </div>
                <div>
                    @if ($subsidy->status === 'APPROVED')
                        <span class="px-3 py-1 text-sm font-semibold rounded bg-green-100 text-green-800">APPROVED</span>
                    @elseif ($subsidy->status === 'REJECTED')
                        <span class="px-3 py-1 text-sm font-semibold rounded bg-red-100 text-red-800">REJECTED</span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded bg-yellow-100 text-yellow-800">PENDING</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-gray-500">Tanggal Pengajuan:</span> <strong>{{ $subsidy->date->format('d M Y') }}</strong></div>
                <div><span class="text-gray-500">Nominal Bantuan:</span> <strong class="text-emerald-700 text-base">Rp {{ number_format($subsidy->amount, 0, ',', '.') }}</strong></div>
                <div><span class="text-gray-500">Disetujui Oleh:</span> <strong>{{ $subsidy->approver?->name ?? 'Belum disetujui' }}</strong></div>
            </div>

            @if ($subsidy->medicalRecord)
                <div class="bg-gray-50 p-4 rounded-md text-sm space-y-1">
                    <h4 class="font-semibold text-gray-700">Terkait Rekam Medis #{{ $subsidy->medicalRecord->id }}:</h4>
                    <p>Dokter: {{ $subsidy->medicalRecord->doctor->name }}</p>
                    <p>Diagnosis: {{ $subsidy->medicalRecord->diagnosis ?? '-' }}</p>
                    <p>Tindakan: {{ $subsidy->medicalRecord->treatment ?? '-' }}</p>
                </div>
            @endif

            @if ($subsidy->notes)
                <div>
                    <h4 class="text-sm font-semibold text-gray-500 uppercase">Catatan / Alasan:</h4>
                    <p class="text-gray-800 mt-1 whitespace-pre-line">{{ $subsidy->notes }}</p>
                </div>
            @endif

            @can('approve', $subsidy)
                @if ($subsidy->status === 'PENDING')
                    <div class="border-t border-gray-200 pt-4 flex gap-3">
                        <form method="POST" action="{{ route('subsidies.approve', $subsidy) }}">
                            @csrf
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">Setujui Subsidi (Approve)</button>
                        </form>
                        <form method="POST" action="{{ route('subsidies.reject', $subsidy) }}">
                            @csrf
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm">Tolak (Reject)</button>
                        </form>
                    </div>
                @endif
            @endcan

            <div class="border-t border-gray-200 pt-4 flex justify-between">
                <a href="{{ route('subsidies.index') }}" class="text-gray-600 hover:underline text-sm">Kembali ke Daftar</a>
                @can('delete', $subsidy)
                    <form method="POST" action="{{ route('subsidies.destroy', $subsidy) }}" onsubmit="return confirm('Hapus pengajuan subsidi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
