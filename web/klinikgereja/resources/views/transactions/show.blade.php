<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kwitansi / Detail Transaksi: {{ $transaction->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white p-8 shadow-sm sm:rounded-lg space-y-6">
            <!-- Header Kwitansi -->
            <div class="flex justify-between items-center border-b border-gray-200 pb-4">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">KLINIK GEREJA</h3>
                    <p class="text-xs text-gray-500">Pelayanan Kasih & Kesehatan Jemaat & Masyarakat</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-700">No. Invoice: <span class="text-indigo-600">{{ $transaction->invoice_number }}</span></p>
                    <p class="text-xs text-gray-500">Tanggal: {{ $transaction->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <!-- Detail Pasien & Status -->
            <div class="grid grid-cols-2 gap-4 text-sm pb-4 border-b border-gray-200">
                <div>
                    <p class="text-gray-500">Nama Pasien:</p>
                    <p class="font-bold text-gray-900 text-base">{{ $transaction->patient->name }}</p>
                    <p class="text-xs text-gray-500">No. Rekam Medis: {{ $transaction->patient->patient_number }} ({{ $transaction->patient->patient_type }})</p>
                    @if ($transaction->patient->church_member_number)
                        <p class="text-xs text-gray-500">No. Anggota: {{ $transaction->patient->church_member_number }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-gray-500">Status Pembayaran:</p>
                    <div class="mt-1">
                        @if ($transaction->status === 'PAID')
                            <span class="px-3 py-1 text-sm font-bold rounded bg-green-100 text-green-800">LUNAS (PAID)</span>
                        @elseif ($transaction->status === 'UNPAID')
                            <span class="px-3 py-1 text-sm font-bold rounded bg-yellow-100 text-yellow-800">BELUM DIBAYAR</span>
                        @else
                            <span class="px-3 py-1 text-sm font-bold rounded bg-red-100 text-red-800">{{ $transaction->status }}</span>
                        @endif
                    </div>
                    @if ($transaction->paid_at)
                        <p class="text-xs text-gray-500 mt-1">Dibayar: {{ $transaction->paid_at->format('d M Y H:i') }}</p>
                        <p class="text-xs text-gray-500">Kasir: {{ $transaction->cashier?->name ?? '-' }}</p>
                    @endif
                </div>
            </div>

            <!-- Rincian Biaya -->
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-600">Total Biaya Layanan Medis & Obat:</span>
                    <span class="font-medium text-gray-900">Rp {{ number_format($transaction->service_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100 text-emerald-700">
                    <span>Potongan Subsidi Kasih Gereja:</span>
                    <span class="font-semibold">- Rp {{ number_format($transaction->subsidy_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-3 border-b-2 border-gray-900 text-base font-bold text-gray-900">
                    <span>Total yang Harus Dibayar Pasien:</span>
                    <span>Rp {{ number_format($transaction->total_payment, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1 text-xs text-gray-500">
                    <span>Metode Pembayaran:</span>
                    <span>{{ $transaction->payment_method ?? 'CASH' }}</span>
                </div>
            </div>

            <!-- Catatan -->
            @if ($transaction->notes)
                <div class="text-xs text-gray-500">
                    <strong>Catatan:</strong> {{ $transaction->notes }}
                </div>
            @endif

            <!-- Aksi Pembayaran jika belum lunas -->
            @if ($transaction->status === 'UNPAID')
                <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                    <h4 class="font-semibold text-sm text-gray-800 mb-2">Proses Pembayaran Pasien:</h4>
                    <form method="POST" action="{{ route('transactions.pay', $transaction) }}" class="flex items-center gap-3">
                        @csrf
                        <select name="payment_method" class="rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="CASH">Tunai (Cash)</option>
                            <option value="TRANSFER">Transfer Bank / QRIS</option>
                            <option value="SUBSIDY_FULL">Subsidi Penuh Gereja</option>
                        </select>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm font-semibold">
                            Tandai Sudah Dibayar (LUNAS)
                        </button>
                    </form>
                </div>
            @endif

            <!-- Footer Tombol -->
            <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:underline text-sm">Kembali ke Daftar Transaksi</a>
                <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">Cetak Kwitansi</button>
            </div>
        </div>
    </div>
</x-app-layout>
