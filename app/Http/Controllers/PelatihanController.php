<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PelatihanController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        $awal = $request->filled('awal')
            ? Carbon::parse($request->query('awal'))
            : now()->subMonths(6);

        $akhir = $request->filled('akhir')
            ? Carbon::parse($request->query('akhir'))
            : now()->addMonths(3);

        return view('divisi.sdm.pelatihan', [
            'division' => $division,
            'pelatihan' => app(SdmIndikatorService::class)->daftarPelatihan($awal, $akhir),
            'awal' => $awal,
            'akhir' => $akhir,
        ]);
    }
}
