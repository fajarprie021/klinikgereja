<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Transaksi & Pembayaran Pelayanan
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <form method="GET" action="{{ route('transactions.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Invoice / Nama Pasien..." class="rounded-md border-gray-300 shadow-sm text-sm">
                <select name="status" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="PAID" {{ request('status') === 'PAID' ? 'selected' : '' }}>PAID</option>
                    <option value="UNPAID" {{ request('status') === 'UNPAID' ? 'selected' : '' }}>UNPAID</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                </select>
                <button type="submit" class="bg-gray-800 text-white px-3 py-2 rounded-md text-sm">Filter</button>
            </form>

            @can('create', App\Models\Transaction::class)
                <a href="{{ route('transactions.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700">
                    + Buat Tagihan / Transaksi Baru
                </a>
            @endcan
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Pasien</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya Layanan</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Subsidi</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Total Bayar</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="p-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($transactions as $tx)
                        <tr>
                            <td class="p-3 text-sm font-semibold text-gray-900">{{ $tx->invoice_number }}</td>
                            <td class="p-3 text-sm whitespace-nowrap">{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                            <td class="p-3 text-sm">
                                <div class="font-medium text-gray-900">{{ $tx->patient->name }}</div>
                                <div class="text-xs text-gray-500">{{ $tx->patient->patient_type }}</div>
                            </td>
                            <td class="p-3 text-sm">Rp {{ number_format($tx->service_cost, 0, ',', '.') }}</td>
                            <td class="p-3 text-sm text-emerald-600 font-medium">Rp {{ number_format($tx->subsidy_amount, 0, ',', '.') }}</td>
                            <td class="p-3 text-sm font-bold text-gray-900">Rp {{ number_format($tx->total_payment, 0, ',', '.') }}</td>
                            <td class="p-3 text-sm">
                                @if ($tx->status === 'PAID')
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800">LUNAS (PAID)</span>
                                @elseif ($tx->status === 'UNPAID')
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">BELUM LUNAS</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">{{ $tx->status }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-sm whitespace-nowrap">
                                <a href="{{ route('transactions.show', $tx) }}" class="text-indigo-600 hover:underline mr-2">Detail / Kwitansi</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-4 text-center text-sm text-gray-500">Belum ada transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</x-app-layout>
