<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ChurchFund;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Subsidy;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display clinic & financial reports overview.
     */
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : now()->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : now()->endOfMonth();

        // Total Medical Records in period
        $medicalRecordsCount = MedicalRecord::whereBetween('created_at', [$startDate, $endDate])->count();

        // Patients served breakdown
        $jemaatCount = MedicalRecord::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('patient', fn($q) => $q->where('patient_type', 'JEMAAT'))
            ->count();

        $umumCount = MedicalRecord::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('patient', fn($q) => $q->where('patient_type', 'UMUM'))
            ->count();

        // Appointments in period
        $appointmentsCount = Appointment::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count();
        $appointmentsDone = Appointment::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', 'DONE')
            ->count();

        // Financial stats in period
        $totalServiceCost = Transaction::whereBetween('created_at', [$startDate, $endDate])->sum('service_cost');
        $totalSubsidy = Transaction::whereBetween('created_at', [$startDate, $endDate])->sum('subsidy_amount');
        $totalPaymentReceived = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'PAID')
            ->sum('total_payment');

        // Church Funds in period
        $fundInflow = ChurchFund::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('type', 'INFLOW')
            ->sum('amount');
        $fundOutflow = ChurchFund::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('type', 'OUTFLOW')
            ->sum('amount');

        // Doctor activity
        $doctorStats = Doctor::withCount(['medicalRecords' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'medicalRecordsCount',
            'jemaatCount',
            'umumCount',
            'appointmentsCount',
            'appointmentsDone',
            'totalServiceCost',
            'totalSubsidy',
            'totalPaymentReceived',
            'fundInflow',
            'fundOutflow',
            'doctorStats'
        ));
    }
}
