<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Division;
use App\Models\RawatInap;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RawatInapController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');
        $bangsal = $request->query('bangsal');

        $rawatInap = RawatInap::with(['kunjungan.pasien', 'bed.kamar', 'dokter'])
            ->when($cari, function ($query, $cari) {
                $query->whereHas('kunjungan', function ($qk) use ($cari) {
                    $qk->where('no_kunjungan', 'like', "%{$cari}%")
                        ->orWhereHas('pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%")->orWhere('no_rm', 'like', "%{$cari}%"));
                });
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($bangsal, function ($query, $bangsal) {
                $query->whereHas('bed.kamar', fn ($qk) => $qk->where('nama_bangsal', $bangsal));
            })
            ->orderByDesc('tanggal_masuk')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'sedang_dirawat' => RawatInap::whereNull('tanggal_keluar')->count(),
            'bed_terisi' => Bed::where('status', 'terisi')->count(),
            'bed_tersedia' => Bed::where('status', 'tersedia')->count(),
            'total_bed' => Bed::count(),
        ];

        $daftarBangsal = \App\Models\Kamar::select('nama_bangsal')->distinct()->orderBy('nama_bangsal')->pluck('nama_bangsal');

        return view('divisi.rawat-inap', [
            'division' => $division,
            'rawatInap' => $rawatInap,
            'ringkasan' => $ringkasan,
            'daftarBangsal' => $daftarBangsal,
            'cari' => $cari,
            'status' => $status,
            'bangsal' => $bangsal,
        ]);
    }

    public function exportPdf(Request $request, Division $division)
    {
        abort_unless($division->slug === 'layanan', 404);

        $awal = Carbon::parse($request->query('awal', now()->subDays(30)))->startOfDay();
        $akhir = Carbon::parse($request->query('akhir', now()))->endOfDay();
        $bangsal = $request->query('bangsal');

        $rawatInap = RawatInap::with(['kunjungan.pasien', 'bed.kamar', 'dokter'])
            ->whereBetween('tanggal_masuk', [$awal, $akhir])
            ->when($bangsal, function ($query, $bangsal) {
                $query->whereHas('bed.kamar', fn ($q) => $q->where('nama_bangsal', $bangsal));
            })
            ->orderBy('tanggal_masuk')
            ->get();

        $pdf = Pdf::loadView('pdf.rawat-inap', [
            'rawatInap' => $rawatInap,
            'awal' => $awal,
            'akhir' => $akhir,
            'bangsal' => $bangsal,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('rawat-inap-' . $awal->format('Ymd') . '-' . $akhir->format('Ymd') . '.pdf');
    }
}