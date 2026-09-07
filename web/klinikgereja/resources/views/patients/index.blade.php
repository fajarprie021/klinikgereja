<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Patients</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <p class="mb-4 text-green-700">{{ session('success') }}</p>
        @endif
        <a class="text-indigo-600" href="{{ route('patients.create') }}">Add patient</a>
        <div class="mt-4 bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead><tr><th class="p-3 text-left">Number</th><th class="p-3 text-left">Name</th><th class="p-3 text-left">Type</th><th class="p-3 text-left">Actions</th></tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($patients as $patient)
                        <tr><td class="p-3">{{ $patient->patient_number }}</td><td class="p-3">{{ $patient->name }}</td><td class="p-3">{{ $patient->patient_type }}</td><td class="p-3"><a class="text-indigo-600" href="{{ route('patients.show', $patient) }}">View</a></td></tr>
                    @empty
                        <tr><td class="p-3" colspan="4">No patients yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $patients->links() }}</div>
    </div>
</x-app-layout>
