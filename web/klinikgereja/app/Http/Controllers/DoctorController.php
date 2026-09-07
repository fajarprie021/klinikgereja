<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Doctor::class);

        $doctors = Doctor::with('user')->latest()->paginate(20);

        return view('doctors.index', compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Doctor::class);

        $doctorRoleId = Role::where('name', 'Dokter')->value('id');
        $users = User::where('role_id', $doctorRoleId)->whereDoesntHave('doctor')->orderBy('name')->get();

        return view('doctors.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        Doctor::create($request->validated());

        return redirect()->route('doctors.index')->with('success', 'Doctor created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor): View
    {
        Gate::authorize('view', $doctor);

        return view('doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor): View
    {
        Gate::authorize('update', $doctor);

        $doctorRoleId = Role::where('name', 'Dokter')->value('id');
        $users = User::where('role_id', $doctorRoleId)
            ->where(function ($query) use ($doctor) {
                $query->whereDoesntHave('doctor')->orWhere('id', $doctor->user_id);
            })
            ->orderBy('name')->get();

        return view('doctors.edit', compact('doctor', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $doctor->update($request->validated());

        return redirect()->route('doctors.show', $doctor)->with('success', 'Doctor updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor): RedirectResponse
    {
        Gate::authorize('delete', $doctor);
        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', 'Doctor deleted');
    }
}
