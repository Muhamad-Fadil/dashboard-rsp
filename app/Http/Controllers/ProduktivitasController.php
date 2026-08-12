<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ProduktivitasController extends Controller
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

        $produktivitas = app(SdmIndikatorService::class)->produktivitasPerUnit($awal, $akhir);

        return view('divisi.sdm.produktivitas', [
            'division' => $division,
            'produktivitas' => $produktivitas,
            'awal' => $awal,
            'akhir' => $akhir,
        ]);
    }
}