<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Catat Transaksi Kas Dana Pelayanan
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('church-funds.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Transaksi *</label>
                    <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="INFLOW" {{ old('type') === 'INFLOW' ? 'selected' : '' }}>Pemasukan Kas (INFLOW)</option>
                        <option value="OUTFLOW" {{ old('type') === 'OUTFLOW' ? 'selected' : '' }}>Pengeluaran Kas (OUTFLOW)</option>
                    </select>
                    @error('type')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Kategori Kas *</label>
                    <select name="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="DONATION">Donasi Kasih Jemaat / Persembahan</option>
                        <option value="SUBSIDY_DISBURSEMENT">Pencairan Subsidi Pasien</option>
                        <option value="MEDICAL_SUPPLIES">Pembelian Obat & Alat Medis</option>
                        <option value="OPERATIONAL">Biaya Operasional Klinik</option>
                        <option value="OTHER">Lain-lain</option>
                    </select>
                    @error('category')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Transaksi *</label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nominal (Rp) *</label>
                    <input type="number" step="1000" name="amount" required placeholder="Contoh: 500000" value="{{ old('amount') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('amount')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Deskripsi / Keterangan *</label>
                    <textarea name="description" rows="3" required placeholder="Keterangan rincian kas..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                    @error('description')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Catatan Kas</button>
                    <a href="{{ route('church-funds.index') }}" class="text-gray-600 hover:underline">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
