<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubsidyRequest;
use App\Http\Requests\UpdateSubsidyRequest;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Subsidy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SubsidyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Subsidy::class);

        $query = Subsidy::with(['patient', 'medicalRecord', 'approver'])->latest('date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('patient_number', 'like', "%{$search}%")
                    ->orWhere('church_member_number', 'like', "%{$search}%");
            });
        }

        $subsidies = $query->paginate(20)->withQueryString();

        return view('subsidies.index', compact('subsidies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Subsidy::class);

        $patients = Patient::orderBy('name')->get();
        $selectedMedicalRecord = null;

        if ($request->filled('medical_record_id')) {
            $selectedMedicalRecord = MedicalRecord::with('patient')->find($request->medical_record_id);
        }

        return view('subsidies.create', compact('patients', 'selectedMedicalRecord'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubsidyRequest $request): RedirectResponse
    {
        Gate::authorize('create', Subsidy::class);

        $data = $request->validated();
        $status = $data['status'] ?? 'PENDING';
        $approvedBy = null;

        if ($status === 'APPROVED' && $request->user()->hasRole(['Bendahara', 'Super Admin'])) {
            $approvedBy = $request->user()->id;
        }

        $subsidy = Subsidy::create([
            'patient_id' => $data['patient_id'],
            'medical_record_id' => $data['medical_record_id'] ?? null,
            'amount' => $data['amount'],
            'date' => $data['date'],
            'status' => $status,
            'approved_by' => $approvedBy,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('subsidies.show', $subsidy)
            ->with('success', 'Pengajuan subsidi berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subsidy $subsidy): View
    {
        Gate::authorize('view', $subsidy);

        $subsidy->load(['patient', 'medicalRecord.doctor', 'approver']);

        return view('subsidies.show', compact('subsidy'));
    }

    /**
     * Approve the subsidy.
     */
    public function approve(Request $request, Subsidy $subsidy): RedirectResponse
    {
        Gate::authorize('approve', $subsidy);

        $subsidy->update([
            'status' => 'APPROVED',
            'approved_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Subsidi berhasil disetujui.');
    }

    /**
     * Reject the subsidy.
     */
    public function reject(Request $request, Subsidy $subsidy): RedirectResponse
    {
        Gate::authorize('approve', $subsidy);

        $subsidy->update([
            'status' => 'REJECTED',
            'approved_by' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Subsidi telah ditolak.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subsidy $subsidy): RedirectResponse
    {
        Gate::authorize('delete', $subsidy);

        $subsidy->delete();

        return redirect()->route('subsidies.index')
            ->with('success', 'Data subsidi berhasil dihapus.');
    }
}
