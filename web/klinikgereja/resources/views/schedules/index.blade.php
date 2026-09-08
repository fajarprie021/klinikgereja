<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Doctor Schedules</h2></x-slot>
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))<p class="mb-4 text-green-700">{{ session('success') }}</p>@endif
        <a class="text-indigo-600" href="{{ route('schedules.create') }}">Add schedule</a>
        <div class="mt-4 bg-white shadow-sm sm:rounded-lg overflow-hidden"><table class="min-w-full divide-y divide-gray-200"><thead><tr><th class="p-3 text-left">Doctor</th><th class="p-3 text-left">Day</th><th class="p-3 text-left">Time</th><th class="p-3 text-left">Actions</th></tr></thead><tbody class="divide-y divide-gray-200">
            @forelse ($schedules as $schedule)<tr><td class="p-3">{{ $schedule->doctor->name }}</td><td class="p-3">{{ $schedule->day }}</td><td class="p-3">{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td><td class="p-3"><a class="text-indigo-600" href="{{ route('schedules.show', $schedule) }}">View</a></td></tr>@empty<tr><td class="p-3" colspan="4">No schedules yet.</td></tr>@endforelse
        </tbody></table></div><div class="mt-4">{{ $schedules->links() }}</div>
    </div>
</x-app-layout>
