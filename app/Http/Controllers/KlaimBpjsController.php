<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\KlaimBpjs;
use Illuminate\Http\Request;

class KlaimBpjsController extends Controller
{
    public function index(Request $request, Division $division)
    {
        $awal = $request->filled('awal')
            ? $request->awal
            : now()->startOfMonth()->toDateString();

        $akhir = $request->filled('akhir')
            ? $request->akhir
            : now()->toDateString();

        $klaim = KlaimBpjs::with(['pasien', 'kunjungan'])
            ->whereBetween('tanggal_pengajuan', [$awal, $akhir])
            ->latest('tanggal_pengajuan')
            ->get();

        $totalKlaim = $klaim->sum('jumlah_klaim');
        $totalDisetujui = $klaim->sum('jumlah_disetujui');

        $jumlahPengajuan = $klaim->count();

        return view('divisi.keuangan.klaim-bpjs', compact(
            'division',
            'klaim',
            'totalKlaim',
            'totalDisetujui',
            'jumlahPengajuan',
            'awal',
            'akhir'
        ));
    }
}