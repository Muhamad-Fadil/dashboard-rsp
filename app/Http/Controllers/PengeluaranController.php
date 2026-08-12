<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index(Request $request, Division $division)
    {
        $awal = $request->filled('awal')
            ? $request->awal
            : now()->startOfMonth()->toDateString();

        $akhir = $request->filled('akhir')
            ? $request->akhir
            : now()->toDateString();

        $pengeluaran = Pengeluaran::with(['kategori', 'unitKerja'])
            ->whereBetween('tanggal', [$awal, $akhir])
            ->latest('tanggal')
            ->get();

        $totalPengeluaran = $pengeluaran->sum('jumlah');

        return view('divisi.keuangan.pengeluaran', compact(
            'division',
            'pengeluaran',
            'totalPengeluaran',
            'awal',
            'akhir'
        ));
    }
}