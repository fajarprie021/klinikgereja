<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-2xl text-slate-900">Edit Pengguna: {{ $user->name }}</h2></x-slot>
    <div class="py-8 bg-slate-50 min-h-[calc(100vh-4rem)]">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @method('PUT')
                    @include('users.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
