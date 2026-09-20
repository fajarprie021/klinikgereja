<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan Pelayanan & Keuangan Klinik Gereja
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Filter Periode -->
        <div class="bg-white p-4 shadow-sm sm:rounded-lg">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                    Tampilkan Laporan
                </button>
                <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">
                    Cetak Laporan
                </button>
            </form>
        </div>

        <!-- Ringkasan Pelayanan Pasien -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-indigo-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Pasien Diperiksa</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $medicalRecordsCount }}</p>
                <p class="text-xs text-gray-500 mt-1">Jemaat: {{ $jemaatCount }} | Umum: {{ $umumCount }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-blue-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Antrean / Kunjungan</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $appointmentsCount }}</p>
                <p class="text-xs text-gray-500 mt-1">Selesai diperiksa: {{ $appointmentsDone }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-emerald-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Subsidi Diberikan</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalSubsidy, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Bantuan kasih gereja</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-amber-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Kas Masuk dari Pasien</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">Rp {{ number_format($totalPaymentReceived, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Dari total layanan Rp {{ number_format($totalServiceCost, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Ringkasan Kas Pelayanan Gereja -->
        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Ringkasan Kas Pelayanan Gereja (Periode Terpilih)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="p-4 bg-green-50 rounded-lg">
                    <span class="text-gray-600 block">Pemasukan Kas (Donasi/Persembahan):</span>
                    <strong class="text-xl text-green-700">Rp {{ number_format($fundInflow, 0, ',', '.') }}</strong>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <span class="text-gray-600 block">Pengeluaran Kas (Obat/Operasional/Subsidi):</span>
                    <strong class="text-xl text-red-700">Rp {{ number_format($fundOutflow, 0, ',', '.') }}</strong>
                </div>
                <div class="p-4 bg-indigo-50 rounded-lg">
                    <span class="text-gray-600 block">Surplus / Defisit Kas Periode:</span>
                    <strong class="text-xl text-indigo-700">Rp {{ number_format($fundInflow - $fundOutflow, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <!-- Tabel Aktivitas Dokter -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-base font-semibold text-gray-800">Aktivitas Pelayanan Dokter</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Dokter</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Spesialisasi</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Pasien Diperiksa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($doctorStats as $doc)
                        <tr>
                            <td class="p-3 text-sm font-medium text-gray-900">{{ $doc->name }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ $doc->specialization }}</td>
                            <td class="p-3 text-sm font-semibold text-indigo-700">{{ $doc->medical_records_count }} pasien</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-4 text-center text-sm text-gray-500">Belum ada data aktivitas dokter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
