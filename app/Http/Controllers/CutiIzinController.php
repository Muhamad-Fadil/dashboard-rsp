<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CutiIzinController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        $awal = $request->filled('awal')
            ? Carbon::parse($request->query('awal'))
            : now()->subDays(30);

        $akhir = $request->filled('akhir')
            ? Carbon::parse($request->query('akhir'))
            : now();

        $service = app(SdmIndikatorService::class);

        return view('divisi.sdm.cuti-izin', [
            'division' => $division,
            'jumlahCutiIzin' => $service->jumlahCutiAktif($awal, $akhir),
            'cuti' => $service->daftarCuti($awal, $akhir),
            'izinHarian' => $service->daftarIzinHarian($awal, $akhir),
            'awal' => $awal,
            'akhir' => $akhir,
        ]);
    }

    public function exportPdf(Request $request, Division $division)
    {
        abort_unless($division->slug === 'sdm', 404);

        $awal = Carbon::parse($request->query('awal', now()->subDays(30)))->startOfDay();
        $akhir = Carbon::parse($request->query('akhir', now()))->endOfDay();

        $service = app(SdmIndikatorService::class);

        $pdf = Pdf::loadView('pdf.sdm.cuti-izin', [
            'cuti' => $service->daftarCuti($awal, $akhir),
            'izinHarian' => $service->daftarIzinHarian($awal, $akhir),
            'awal' => $awal,
            'akhir' => $akhir,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('cuti-izin-' . $awal->format('Ymd') . '-' . $akhir->format('Ymd') . '.pdf');
    }
}
