<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Patient Detail</h2></x-slot>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-2">
            <p><strong>Number:</strong> {{ $patient->patient_number }}</p><p><strong>Name:</strong> {{ $patient->name }}</p><p><strong>Type:</strong> {{ $patient->patient_type }}</p><p><strong>NIK:</strong> {{ $patient->nik ?: '-' }}</p><p><strong>Phone:</strong> {{ $patient->phone ?: '-' }}</p><p><strong>Address:</strong> {{ $patient->address ?: '-' }}</p>
            <a class="text-indigo-600" href="{{ route('patients.edit', $patient) }}">Edit</a>
            <form method="POST" action="{{ route('patients.destroy', $patient) }}">@csrf @method('DELETE')<button class="text-red-600" type="submit">Delete</button></form>
        </div>
    </div>
</x-app-layout>
