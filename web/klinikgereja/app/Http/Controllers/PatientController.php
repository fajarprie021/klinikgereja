<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Patient::class);

        $patients = Patient::query()->orderBy('id', 'desc')->paginate(20);

        return view('patients.index', compact('patients'));
    }

    public function create(): View
    {
        Gate::authorize('create', Patient::class);

        return view('patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        Gate::authorize('create', Patient::class);

        Patient::create($request->validated());

        return redirect()->route('patients.index')->with('success', 'Patient created');
    }

    public function show(Patient $patient): View
    {
        Gate::authorize('view', $patient);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        Gate::authorize('update', $patient);

        return view('patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('update', $patient);

        $patient->update($request->validated());

        return redirect()->route('patients.show', $patient)->with('success', 'Patient updated');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        Gate::authorize('delete', $patient);

        $patient->delete();

        return redirect()->route('patients.index')->with('success', 'Patient deleted');
    }
}
