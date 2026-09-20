<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Rekam Medis #{{ $medicalRecord->id }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('medical-records.update', $medicalRecord) }}">
                @method('PUT')
                @include('medical_records.form')
            </form>
        </div>
    </div>
</x-app-layout>
