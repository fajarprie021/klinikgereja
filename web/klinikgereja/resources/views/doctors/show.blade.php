<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Doctor Detail</h2></x-slot>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-2"><p><strong>Name:</strong> {{ $doctor->name }}</p><p><strong>Specialization:</strong> {{ $doctor->specialization }}</p><p><strong>Email:</strong> {{ $doctor->user->email }}</p><a class="text-indigo-600" href="{{ route('doctors.edit', $doctor) }}">Edit</a><form method="POST" action="{{ route('doctors.destroy', $doctor) }}">@csrf @method('DELETE')<button class="text-red-600" type="submit">Delete</button></form></div></div>
</x-app-layout>
