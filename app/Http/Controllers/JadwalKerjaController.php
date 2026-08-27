<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
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
}
