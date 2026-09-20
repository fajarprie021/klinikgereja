<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rekam Medis (Medical Records)
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('medical-records.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pasien / diagnosis / keluhan..." class="rounded-md border-gray-300 shadow-sm text-sm">
                <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-md text-sm">Cari</button>
            </form>

            @can('create', App\Models\MedicalRecord::class)
                <a href="{{ route('medical-records.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                    + Input Rekam Medis Baru
                </a>
            @endcan
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">No. Pasien / Nama</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Dokter</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Keluhan</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Diagnosis</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Tanda Vital</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($medicalRecords as $record)
                        <tr>
                            <td class="p-3 text-sm whitespace-nowrap">{{ $record->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-3 text-sm">
                                <span class="font-medium text-gray-900">{{ $record->patient->name }}</span>
                                <div class="text-xs text-gray-500">{{ $record->patient->patient_number }} ({{ $record->patient->patient_type }})</div>
                            </td>
                            <td class="p-3 text-sm">{{ $record->doctor->name }}</td>
                            <td class="p-3 text-sm">{{ Str::limit($record->complaint, 40) }}</td>
                            <td class="p-3 text-sm">{{ Str::limit($record->diagnosis ?? '-', 40) }}</td>
                            <td class="p-3 text-xs text-gray-600">
                                @if ($record->vitalSign)
                                    <div>TD: {{ $record->vitalSign->blood_pressure ?? '-' }}</div>
                                    <div>Suhu: {{ $record->vitalSign->temperature ? $record->vitalSign->temperature.'°C' : '-' }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="p-3 text-sm whitespace-nowrap">
                                <a href="{{ route('medical-records.show', $record) }}" class="text-indigo-600 hover:underline mr-2">Lihat</a>
                                @can('update', $record)
                                    <a href="{{ route('medical-records.edit', $record) }}" class="text-yellow-600 hover:underline">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-sm text-gray-500">Belum ada data rekam medis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $medicalRecords->links() }}
        </div>
    </div>
</x-app-layout>
