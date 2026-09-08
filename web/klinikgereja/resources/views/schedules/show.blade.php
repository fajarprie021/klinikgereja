<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Schedule Detail</h2></x-slot>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-2"><p><strong>Doctor:</strong> {{ $schedule->doctor->name }}</p><p><strong>Day:</strong> {{ $schedule->day }}</p><p><strong>Time:</strong> {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</p><a class="text-indigo-600" href="{{ route('schedules.edit', $schedule) }}">Edit</a><form method="POST" action="{{ route('schedules.destroy', $schedule) }}">@csrf @method('DELETE')<button class="text-red-600" type="submit">Delete</button></form></div></div>
</x-app-layout>
