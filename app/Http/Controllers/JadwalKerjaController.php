<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class JadwalKerjaController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->query('tanggal'))
            : now();

        return view('divisi.sdm.jadwal-kerja', [
            'division' => $division,
            'jadwal' => app(SdmIndikatorService::class)->jadwalKerja($tanggal),
            'tanggal' => $tanggal,
        ]);
    }

    public function exportPdf(Request $request, Division $division)
    {
        abort_unless($division->slug === 'sdm', 404);

        $tanggal = Carbon::parse($request->query('tanggal', now()));

        $pdf = Pdf::loadView('pdf.sdm.jadwal-kerja', [
            'jadwal' => app(SdmIndikatorService::class)->jadwalKerja($tanggal),
            'tanggal' => $tanggal,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('jadwal-kerja-' . $tanggal->format('Ymd') . '.pdf');
    }
}
