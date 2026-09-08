<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Edit Appointment</h2></x-slot>
    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8"><form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-4 bg-white p-6 shadow-sm sm:rounded-lg">@csrf @method('PUT') @include('appointments.form')<label class="block"><span class="text-gray-700">Status</span><select class="mt-1 block w-full rounded border-gray-300" name="status">@foreach (['WAITING','CHECKED','DONE','CANCELLED'] as $status)<option value="{{ $status }}" @selected($appointment->status === $status)>{{ $status }}</option>@endforeach</select></label><button class="rounded bg-indigo-600 px-4 py-2 text-white">Update</button></form></div>
</x-app-layout>
