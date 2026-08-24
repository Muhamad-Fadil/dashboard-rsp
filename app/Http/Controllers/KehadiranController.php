<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class KehadiranController extends Controller
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

        $status = $request->query('status');

        $service = app(SdmIndikatorService::class);

        return view('divisi.sdm.kehadiran', [
            'division' => $division,
            'persentaseKehadiran' => $service->persentaseKehadiran($awal, $akhir),
            'rekapStatus' => $service->rekapStatusAbsensi($awal, $akhir),
            'absensi' => $service->daftarAbsensi($awal, $akhir, $status)->take(200),
            'awal' => $awal,
            'akhir' => $akhir,
            'statusFilter' => $status,
        ]);
    }
}
