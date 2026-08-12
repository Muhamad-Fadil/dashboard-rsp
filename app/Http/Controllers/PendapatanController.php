<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pendapatan;
use Illuminate\Http\Request;

class PendapatanController extends Controller
{
    public function index(Request $request, Division $division)
    {
        $awal = $request->filled('awal')
            ? $request->awal
            : now()->startOfMonth()->toDateString();

        $akhir = $request->filled('akhir')
            ? $request->akhir
            : now()->toDateString();

        $pendapatan = Pendapatan::with(['kategori', 'unitKerja'])
            ->whereBetween('tanggal', [$awal, $akhir])
            ->latest('tanggal')
            ->get();

        $totalPendapatan = $pendapatan->sum('jumlah');

        return view('divisi.keuangan.pendapatan', compact(
            'division',
            'pendapatan',
            'totalPendapatan',
            'awal',
            'akhir'
        ));
    }
}