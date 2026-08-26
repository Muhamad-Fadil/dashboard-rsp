<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pasien;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PasienController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');

        $pasien = Pasien::with([
                'jenisPembayaran',
                'wilayah',
                'kunjungan' => fn ($q) => $q->with(['poli', 'dokter'])->orderByDesc('waktu_daftar'),
            ])
            ->when($cari, function ($query, $cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('nama', 'like', "%{$cari}%")
                        ->orWhere('no_rm', 'like', "%{$cari}%")
                        ->orWhere('no_registrasi', 'like', "%{$cari}%")
                        ->orWhere('nik', 'like', "%{$cari}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('divisi.layanan.pasien', [
            'division' => $division,
            'pasien' => $pasien,
            'cari' => $cari,
        ]);
    }

    public function exportPdf(Request $request, Division $division)
    {
        abort_unless($division->slug === 'layanan', 404);

        $awal = Carbon::parse($request->query('awal', now()->subDays(30)))->startOfDay();
        $akhir = Carbon::parse($request->query('akhir', now()))->endOfDay();

        $pasien = Pasien::with('jenisPembayaran')
            ->whereBetween('tanggal_registrasi', [$awal, $akhir])
            ->orderBy('nama')
            ->get();
        $pdf = Pdf::loadView('pdf.layanan.pasien', [
            'pasien' => $pasien,
            'awal' => $awal,
            'akhir' => $akhir,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-pasien-' . $awal->format('Ymd') . '-' . $akhir->format('Ymd') . '.pdf');
    }
}