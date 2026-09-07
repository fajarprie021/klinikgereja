<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Edit Patient</h2></x-slot>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('patients.update', $patient) }}" class="space-y-4 bg-white p-6 shadow-sm sm:rounded-lg">
            @csrf @method('PUT')
            @include('patients.form')
            <button class="rounded bg-indigo-600 px-4 py-2 text-white">Update</button>
        </form>
    </div>
</x-app-layout>
