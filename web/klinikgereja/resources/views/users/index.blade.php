<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-wider text-indigo-600 font-semibold">Super Admin</p>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">Manajemen Pengguna</h2>
            </div>
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700">+ Tambah Pengguna</a>
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <p class="text-sm text-slate-500">Kelola akun petugas, dokter, perawat, bendahara, dan pasien beserta status aksesnya.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4 text-left text-xs uppercase text-slate-500">Nama</th>
                                <th class="p-4 text-left text-xs uppercase text-slate-500">Username</th>
                                <th class="p-4 text-left text-xs uppercase text-slate-500">Role</th>
                                <th class="p-4 text-left text-xs uppercase text-slate-500">Status</th>
                                <th class="p-4 text-left text-xs uppercase text-slate-500">Dibuat</th>
                                <th class="p-4 text-left text-xs uppercase text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="p-4 text-sm font-semibold text-slate-900">{{ $user->name }}</td>
                                    <td class="p-4 text-sm text-slate-600">{{ $user->username }}</td>
                                    <td class="p-4 text-sm"><span class="px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 font-semibold">{{ $user->role?->name ?? 'Tanpa role' }}</span></td>
                                    <td class="p-4 text-sm"><span class="px-2 py-1 rounded-full {{ $user->status === 'ACTIVE' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $user->status }}</span></td>
                                    <td class="p-4 text-sm text-slate-500">{{ $user->created_at?->format('d M Y') }}</td>
                                    <td class="p-4 text-sm whitespace-nowrap">
                                        <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:underline mr-3">Edit</a>
                                        @if ($user->id !== Auth::id())
                                            <form class="inline" method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600 hover:underline" type="submit">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-6 text-center text-sm text-slate-500">Belum ada pengguna.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>
