<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Rekam Medis & Pemeriksaan
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        @if (isset($selectedAppointment))
            <div class="mb-4 bg-blue-50 border border-blue-200 p-4 rounded-md">
                <h4 class="font-semibold text-blue-900">Pemeriksaan dari Antrean:</h4>
                <p class="text-sm text-blue-800">
                    No. Antrean: <strong>#{{ $selectedAppointment->queue_number }}</strong> |
                    Pasien: <strong>{{ $selectedAppointment->patient->name }}</strong> ({{ $selectedAppointment->patient->patient_number }}) |
                    Dokter: <strong>{{ $selectedAppointment->doctor->name }}</strong>
                </p>
            </div>
        @endif

        <div class="bg-white p-6 shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('medical-records.store') }}">
                @include('medical_records.form')
            </form>
        </div>
    </div>
</x-app-layout>
