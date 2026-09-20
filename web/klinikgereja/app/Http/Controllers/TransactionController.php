<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Subsidy;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Transaction::class);

        $query = Transaction::with(['patient', 'medicalRecord.doctor', 'cashier'])->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('patient_number', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', Transaction::class);

        $patients = Patient::orderBy('name')->get();
        $selectedMedicalRecord = null;
        $suggestedSubsidy = 0;

        if ($request->filled('medical_record_id')) {
            $selectedMedicalRecord = MedicalRecord::with(['patient', 'doctor', 'subsidy'])->find($request->medical_record_id);
            if ($selectedMedicalRecord && $selectedMedicalRecord->subsidy && $selectedMedicalRecord->subsidy->status === 'APPROVED') {
                $suggestedSubsidy = $selectedMedicalRecord->subsidy->amount;
            }
        }

        return view('transactions.create', compact('patients', 'selectedMedicalRecord', 'suggestedSubsidy'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        Gate::authorize('create', Transaction::class);

        $data = $request->validated();
        $serviceCost = (float) $data['service_cost'];
        $subsidyAmount = (float) ($data['subsidy_amount'] ?? 0);
        $totalPayment = max(0, $serviceCost - $subsidyAmount);
        $status = $data['status'] ?? 'UNPAID';

        $transaction = DB::transaction(function () use ($data, $serviceCost, $subsidyAmount, $totalPayment, $status, $request) {
            $today = now()->format('Ymd');
            $countToday = Transaction::whereDate('created_at', now()->toDateString())->count() + 1;
            $invoiceNumber = 'INV-' . $today . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            $paidAt = ($status === 'PAID') ? now() : null;
            $cashierId = ($status === 'PAID') ? $request->user()->id : null;

            return Transaction::create([
                'invoice_number' => $invoiceNumber,
                'patient_id' => $data['patient_id'],
                'medical_record_id' => $data['medical_record_id'] ?? null,
                'service_cost' => $serviceCost,
                'subsidy_amount' => $subsidyAmount,
                'total_payment' => $totalPayment,
                'payment_method' => $data['payment_method'] ?? ($totalPayment == 0 ? 'SUBSIDY_FULL' : 'CASH'),
                'status' => $status,
                'paid_at' => $paidAt,
                'cashier_id' => $cashierId,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaksi pembayaran berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction): View
    {
        Gate::authorize('view', $transaction);

        $transaction->load(['patient', 'medicalRecord.doctor', 'medicalRecord.vitalSign', 'cashier']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Process payment for an unpaid transaction.
     */
    public function pay(Request $request, Transaction $transaction): RedirectResponse
    {
        Gate::authorize('update', $transaction);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:30'],
        ]);

        $transaction->update([
            'status' => 'PAID',
            'payment_method' => $validated['payment_method'],
            'paid_at' => now(),
            'cashier_id' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil diselesaikan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction): RedirectResponse
    {
        Gate::authorize('delete', $transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
