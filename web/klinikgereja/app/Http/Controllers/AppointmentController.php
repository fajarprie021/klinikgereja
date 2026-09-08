<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Appointment::class);
        $appointments = Appointment::with(['patient', 'doctor'])->latest('date')->latest('queue_number')->paginate(20);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Appointment::class);
        $patients = Patient::orderBy('name')->get();
        $doctors = Doctor::orderBy('name')->get();

        return view('appointments.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $appointment = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $queueNumber = ((int) Appointment::where('doctor_id', $data['doctor_id'])
                ->whereDate('date', $data['date'])
                ->lockForUpdate()
                ->max('queue_number')) + 1;

            return Appointment::create([
                ...$data,
                'queue_number' => $queueNumber,
                'status' => 'WAITING',
            ]);
        });

        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment): View
    {
        Gate::authorize('view', $appointment);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment): View
    {
        Gate::authorize('update', $appointment);
        $patients = Patient::orderBy('name')->get();
        $doctors = Doctor::orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update($request->validated());

        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
        Gate::authorize('delete', $appointment);
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted');
    }

    public function status(Appointment $appointment, string $status): RedirectResponse
    {
        Gate::authorize('updateStatus', $appointment);

        abort_unless(in_array($status, ['WAITING', 'CHECKED', 'DONE', 'CANCELLED'], true), 404);

        $allowedTransitions = [
            'WAITING' => ['CHECKED', 'CANCELLED'],
            'CHECKED' => ['DONE', 'CANCELLED'],
            'DONE' => [],
            'CANCELLED' => [],
        ];

        abort_unless(in_array($status, $allowedTransitions[$appointment->status], true), 422);
        $appointment->update(['status' => $status]);

        return redirect()->route('appointments.show', $appointment)->with('success', 'Appointment status updated');
    }
}
