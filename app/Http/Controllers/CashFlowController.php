<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\KeuanganIndikatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashFlowController extends Controller
{
    public function index(
        Request $request,
        Division $division,
        KeuanganIndikatorService $keuanganService
    ) {
        $awal = $request->filled('awal')
            ? Carbon::parse($request->awal)->startOfDay()
            : now()->startOfMonth();

        $akhir = $request->filled('akhir')
            ? Carbon::parse($request->akhir)->endOfDay()
            : now()->endOfDay();

        $totalPendapatan = $keuanganService->totalPendapatan($awal, $akhir);
        $totalPengeluaran = $keuanganService->totalBelanja($awal, $akhir);

        $cashFlow = $totalPendapatan - $totalPengeluaran;

        $trenBulanan = $keuanganService->trenBulanan(6);

        return view('divisi.keuangan.cash-flow', compact(
            'division',
            'awal',
            'akhir',
            'totalPendapatan',
            'totalPengeluaran',
            'cashFlow',
            'trenBulanan'
        ));
    }
}