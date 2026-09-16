<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\KlaimBpjs;
use Illuminate\Http\Request;

class KlaimBpjsController extends Controller
{
    public function index(Request $request, Division $division)
    {
        // kalau user belum pilih tanggal sendiri, otomatis pakai rentang tanggal
        // data klaim BPJS yang benar-benar ada (bukan "bulan ini"), supaya tidak
        // kelihatan kosong padahal datanya ada tapi di luar rentang tanggal default.
        $rentangData = KlaimBpjs::selectRaw('min(tanggal_pengajuan) as awal, max(tanggal_pengajuan) as akhir')->first();

        $awal = $request->filled('awal')
            ? $request->awal
            : ($rentangData?->awal ?? now()->startOfMonth()->toDateString());

        $akhir = $request->filled('akhir')
            ? $request->akhir
            : ($rentangData?->akhir ?? now()->toDateString());

        $cari = trim((string) $request->query('cari', ''));

        $queryDasar = KlaimBpjs::whereBetween('tanggal_pengajuan', [$awal, $akhir]);

        // pencarian bebas: cocok di jenis BPJS, no. SEP, no. registrasi, status, atau keterangan
        if ($cari !== '') {
            $queryDasar->where(function ($q) use ($cari) {
                $q->where('jenis_bpjs', 'like', "%{$cari}%")
                    ->orWhere('no_sep', 'like', "%{$cari}%")
                    ->orWhere('no_reg', 'like', "%{$cari}%")
                    ->orWhere('status', 'like', "%{$cari}%")
                    ->orWhere('keterangan', 'like', "%{$cari}%")
                    ->orWhereHas('pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%"));
            });
        }

        $totalKlaim = (clone $queryDasar)->sum('jumlah_klaim');
        $totalDisetujui = (clone $queryDasar)->sum('jumlah_disetujui');
        $jumlahPengajuan = (clone $queryDasar)->count();

        $klaim = (clone $queryDasar)
            ->with(['pasien', 'kunjungan'])
            ->latest('tanggal_pengajuan')
            ->paginate(25)
            ->withQueryString();

        return view('divisi.keuangan.klaim-bpjs', compact(
            'division',
            'klaim',
            'totalKlaim',
            'totalDisetujui',
            'jumlahPengajuan',
            'awal',
            'akhir',
            'cari'
        ));
    }
}