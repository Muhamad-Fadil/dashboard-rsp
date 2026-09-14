<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Kunjungan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class IgdController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');
        $awal = $request->filled('awal') ? Carbon::parse($request->query('awal'))->startOfDay() : now()->subDays(30);
        $akhir = $request->filled('akhir') ? Carbon::parse($request->query('akhir'))->endOfDay() : now();

        $kunjungan = Kunjungan::with(['pasien.jenisPembayaran', 'poli', 'dokter', 'operator'])
            ->where('jenis_kunjungan', 'igd')
            ->whereBetween('waktu_daftar', [$awal, $akhir])
            ->when($cari, function ($query, $cari) {
                $query->where('no_kunjungan', 'like', "%{$cari}%")
                    ->orWhereHas('pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%")->orWhere('no_rm', 'like', "%{$cari}%"));
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('waktu_daftar')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'total' => Kunjungan::where('jenis_kunjungan', 'igd')->whereBetween('waktu_daftar', [$awal, $akhir])->count(),
            'selesai' => Kunjungan::where('jenis_kunjungan', 'igd')->whereBetween('waktu_daftar', [$awal, $akhir])->where('status', 'selesai')->count(),
            'batal' => Kunjungan::where('jenis_kunjungan', 'igd')->whereBetween('waktu_daftar', [$awal, $akhir])->where('status', 'batal')->count(),
        ];

        return view('divisi.layanan.igd', compact('division', 'kunjungan', 'ringkasan', 'cari', 'status', 'awal', 'akhir'));
    }

    public function exportPdf(Request $request, Division $division)
    {
        abort_unless($division->slug === 'layanan', 404);

        $tanggalTerakhir = Kunjungan::where('jenis_kunjungan', 'igd')->max('waktu_daftar');
        $defaultAkhir = $tanggalTerakhir ? Carbon::parse($tanggalTerakhir)->endOfDay() : now()->endOfDay();
        $defaultAwal = $defaultAkhir->copy()->subDays(29)->startOfDay();

        $awal = $request->filled('awal') ? Carbon::parse($request->query('awal'))->startOfDay() : $defaultAwal;
        $akhir = $request->filled('akhir') ? Carbon::parse($request->query('akhir'))->endOfDay() : $defaultAkhir;

        $kunjungan = Kunjungan::with(['pasien.jenisPembayaran', 'poli', 'dokter'])
            ->where('jenis_kunjungan', 'igd')
            ->whereBetween('waktu_daftar', [$awal, $akhir])
            ->orderBy('waktu_daftar')
            ->get();

        $pdf = Pdf::loadView('pdf.layanan.igd', compact('kunjungan', 'awal', 'akhir'))->setPaper('a4', 'landscape');

        return $pdf->stream('igd-' . $awal->format('Ymd') . '-' . $akhir->format('Ymd') . '.pdf');
    }
}