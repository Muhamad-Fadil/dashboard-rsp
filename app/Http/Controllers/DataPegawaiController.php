<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataPegawaiController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');
        $statusYangDidukung = ['pns', 'pppk', 'blu', 'mitra', 'magang'];
        $status = in_array($status, $statusYangDidukung, true) ? $status : null;

        return view('divisi.sdm.data-pegawai', [
            'division' => $division,
            'pegawai' => app(SdmIndikatorService::class)->daftarLengkapPegawai($cari, true, $status),
            'cari' => $cari,
            'status' => $status,
        ]);
    }

    /**
     * Data Pegawai itu data induk (snapshot pegawai aktif sekarang), bukan data per-periode
     * kayak kunjungan pasien — jadi PDF-nya tanpa filter tanggal, cukup ikut kata kunci
     * pencarian yang lagi dipakai di halaman (kalau ada).
     */
    public function exportPdf(Request $request, Division $division)
    {
        abort_unless($division->slug === 'sdm', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');
        $statusYangDidukung = ['pns', 'pppk', 'blu', 'mitra', 'magang'];
        $status = in_array($status, $statusYangDidukung, true) ? $status : null;

        $pegawai = app(SdmIndikatorService::class)->daftarLengkapPegawai($cari, false, $status);

        $pdf = Pdf::loadView('pdf.sdm.data-pegawai', [
            'pegawai' => $pegawai,
            'cari' => $cari,
            'status' => $status,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('data-pegawai-' . now()->format('Ymd-His') . '.pdf');
    }
}
