<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pendapatan;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function exportPdf(Request $request, Division $division)
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

        $pdf = Pdf::loadView('pdf.keuangan.pendapatan', [
            'pendapatan' => $pendapatan,
            'awal' => $awal,
            'akhir' => $akhir,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-pendapatan.pdf');
    }
}