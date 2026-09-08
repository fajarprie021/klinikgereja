<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        Gate::authorize('viewAny', Schedule::class);
        $schedules = Schedule::with('doctor')->orderBy('day')->orderBy('start_time')->paginate(20);

        return view('schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Schedule::class);
        $doctors = Doctor::orderBy('name')->get();

        return view('schedules.create', compact('doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->ensureNoOverlap($data);
        Schedule::create($data);

        return redirect()->route('schedules.index')->with('success', 'Schedule created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule): View
    {
        Gate::authorize('view', $schedule);

        return view('schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule): View
    {
        Gate::authorize('update', $schedule);
        $doctors = Doctor::orderBy('name')->get();

        return view('schedules.edit', compact('schedule', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $data = $request->validated();
        $this->ensureNoOverlap($data, $schedule->id);
        $schedule->update($data);

        return redirect()->route('schedules.show', $schedule)->with('success', 'Schedule updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule): RedirectResponse
    {
        Gate::authorize('delete', $schedule);
        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted');
    }

    private function ensureNoOverlap(array $data, ?int $ignoreId = null): void
    {
        $query = Schedule::query()
            ->where('doctor_id', $data['doctor_id'])
            ->where('day', $data['day'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time']);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'start_time' => 'Schedule overlaps an existing schedule for this doctor and day.',
            ]);
        }
    }
}
