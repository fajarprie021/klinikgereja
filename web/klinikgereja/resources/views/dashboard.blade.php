<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <p class="text-xs uppercase tracking-wider text-indigo-600 font-semibold">Portal Klinik Gereja</p>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">Dashboard Operasional</h2>
            </div>
            <div class="text-sm text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</div>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-gradient-to-r from-indigo-700 to-indigo-500 rounded-2xl p-6 sm:p-8 text-white shadow-lg shadow-indigo-100">
                <p class="text-indigo-100 text-sm">Selamat datang kembali,</p>
                <h1 class="text-2xl sm:text-3xl font-bold mt-1">{{ Auth::user()->name }}</h1>
                <p class="text-indigo-100 mt-2 max-w-2xl">Pantau pelayanan pasien, antrean pemeriksaan, rekam medis, dan tindak lanjut administrasi dari sini.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <p class="text-xs uppercase font-semibold text-slate-500">Total Pasien</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ $totalPatients }}</p>
                    <a href="{{ route('patients.index') }}" class="text-xs text-indigo-600 hover:underline">Lihat data pasien &rarr;</a>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <p class="text-xs uppercase font-semibold text-slate-500">Antrean Hari Ini</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $todayAppointments }}</p>
                    <span class="text-xs text-slate-500">{{ $todayChecked }} sudah diproses</span>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <p class="text-xs uppercase font-semibold text-slate-500">Rekam Medis</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalMedicalRecords }}</p>
                    <a href="{{ route('medical-records.index') }}" class="text-xs text-indigo-600 hover:underline">Buka rekam medis &rarr;</a>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <p class="text-xs uppercase font-semibold text-slate-500">Subsidi Pending</p>
                    <p class="text-3xl font-bold text-amber-600 mt-2">{{ $pendingSubsidies }}</p>
                    <a href="{{ route('subsidies.index') }}" class="text-xs text-indigo-600 hover:underline">Tinjau pengajuan &rarr;</a>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                    <p class="text-xs uppercase font-semibold text-slate-500">Role Aktif</p>
                    <p class="text-xl font-bold text-purple-600 mt-3">{{ Auth::user()->role?->name ?? 'Belum diatur' }}</p>
                    <span class="text-xs text-slate-500">Akses berbasis peran</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900">Antrean Terbaru</h3>
                            <p class="text-xs text-slate-500 mt-1">Aktivitas pendaftaran terakhir</p>
                        </div>
                        <a href="{{ route('appointments.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($recentAppointments as $appointment)
                            <div class="p-4 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold">#{{ $appointment->queue_number }}</div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-900">{{ $appointment->patient->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $appointment->doctor->name }} &middot; {{ $appointment->date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $appointment->status === 'DONE' ? 'bg-emerald-100 text-emerald-700' : ($appointment->status === 'CHECKED' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">{{ $appointment->status }}</span>
                            </div>
                        @empty
                            <p class="p-5 text-sm text-slate-500">Belum ada antrean.</p>
                        @endforelse
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900">Rekam Medis Terbaru</h3>
                            <p class="text-xs text-slate-500 mt-1">Pemeriksaan yang baru dicatat</p>
                        </div>
                        <a href="{{ route('medical-records.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($recentMedicalRecords as $record)
                            <div class="p-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-sm text-slate-900">{{ $record->patient->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $record->diagnosis ?? 'Diagnosis belum diisi' }} &middot; {{ $record->doctor->name }}</p>
                                </div>
                                <a href="{{ route('medical-records.show', $record) }}" class="text-xs text-indigo-600 hover:underline">Detail</a>
                            </div>
                        @empty
                            <p class="p-5 text-sm text-slate-500">Belum ada rekam medis.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
