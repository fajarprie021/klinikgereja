<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChurchFundRequest;
use App\Models\ChurchFund;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ChurchFundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ChurchFund::class);

        $query = ChurchFund::with('user')->latest('date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $totalInflow = ChurchFund::where('type', 'INFLOW')->sum('amount');
        $totalOutflow = ChurchFund::where('type', 'OUTFLOW')->sum('amount');
        $balance = $totalInflow - $totalOutflow;

        $funds = $query->paginate(20)->withQueryString();

        return view('church_funds.index', compact('funds', 'totalInflow', 'totalOutflow', 'balance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', ChurchFund::class);

        return view('church_funds.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChurchFundRequest $request): RedirectResponse
    {
        Gate::authorize('create', ChurchFund::class);

        $data = $request->validated();

        ChurchFund::create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('church-funds.index')
            ->with('success', 'Catatan kas dana gereja berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChurchFund $churchFund): RedirectResponse
    {
        Gate::authorize('delete', $churchFund);

        $churchFund->delete();

        return redirect()->route('church-funds.index')
            ->with('success', 'Catatan kas berhasil dihapus.');
    }
}
