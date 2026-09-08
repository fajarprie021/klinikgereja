<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Appointments</h2></x-slot>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))<p class="mb-4 text-green-700">{{ session('success') }}</p>@endif
        @can('create', App\Models\Appointment::class)<a class="text-indigo-600" href="{{ route('appointments.create') }}">Add appointment</a>@endcan
        <div class="mt-4 bg-white shadow-sm sm:rounded-lg overflow-hidden"><table class="min-w-full divide-y divide-gray-200"><thead><tr><th class="p-3 text-left">Queue</th><th class="p-3 text-left">Date</th><th class="p-3 text-left">Patient</th><th class="p-3 text-left">Doctor</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Actions</th></tr></thead><tbody class="divide-y divide-gray-200">
            @forelse ($appointments as $appointment)<tr><td class="p-3">{{ $appointment->queue_number }}</td><td class="p-3">{{ $appointment->date->format('Y-m-d') }}</td><td class="p-3">{{ $appointment->patient->name }}</td><td class="p-3">{{ $appointment->doctor->name }}</td><td class="p-3">{{ $appointment->status }}</td><td class="p-3"><a class="text-indigo-600" href="{{ route('appointments.show', $appointment) }}">View</a></td></tr>@empty<tr><td class="p-3" colspan="6">No appointments yet.</td></tr>@endforelse
        </tbody></table></div><div class="mt-4">{{ $appointments->links() }}</div>
    </div>
</x-app-layout>
