<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kas & Dana Pelayanan Kesehatan Gereja
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Saldo Kas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-green-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Pemasukan (Inflow)</p>
                <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($totalInflow, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-red-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Pengeluaran (Outflow)</p>
                <p class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($totalOutflow, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-sm border-l-4 border-indigo-500">
                <p class="text-xs text-gray-500 uppercase font-semibold">Saldo Kas Pelayanan</p>
                <p class="text-2xl font-bold text-indigo-700 mt-1">Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('church-funds.index') }}" class="flex gap-2">
                <select name="type" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">-- Semua Arus Kas --</option>
                    <option value="INFLOW" {{ request('type') === 'INFLOW' ? 'selected' : '' }}>Pemasukan (INFLOW)</option>
                    <option value="OUTFLOW" {{ request('type') === 'OUTFLOW' ? 'selected' : '' }}>Pengeluaran (OUTFLOW)</option>
                </select>
                <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-md text-sm">Filter</button>
            </form>

            @can('create', App\Models\ChurchFund::class)
                <a href="{{ route('church-funds.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                    + Catat Arus Kas Baru
                </a>
            @endcan
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi / Keterangan</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Nominal</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Dicatat Oleh</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($funds as $fund)
                        <tr>
                            <td class="p-3 text-sm whitespace-nowrap">{{ $fund->date->format('Y-m-d') }}</td>
                            <td class="p-3 text-sm">
                                @if ($fund->type === 'INFLOW')
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">MASUK</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">KELUAR</span>
                                @endif
                            </td>
                            <td class="p-3 text-sm font-medium text-gray-700">{{ $fund->category }}</td>
                            <td class="p-3 text-sm">{{ $fund->description }}</td>
                            <td class="p-3 text-sm font-bold {{ $fund->type === 'INFLOW' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $fund->type === 'INFLOW' ? '+' : '-' }} Rp {{ number_format($fund->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-sm text-gray-600">{{ $fund->user?->name ?? '-' }}</td>
                            <td class="p-3 text-sm">
                                @can('delete', $fund)
                                    <form method="POST" action="{{ route('church-funds.destroy', $fund) }}" onsubmit="return confirm('Hapus catatan kas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline text-xs">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-sm text-gray-500">Belum ada catatan kas dana pelayanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $funds->links() }}
        </div>
    </div>
</x-app-layout>
