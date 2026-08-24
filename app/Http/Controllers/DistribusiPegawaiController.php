<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Illuminate\View\View;

class DistribusiPegawaiController extends Controller
{
    public function index(Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        return view('divisi.sdm.distribusi', [
            'division' => $division,
            'distribusi' => app(SdmIndikatorService::class)->distribusiPerUnitDetail(),
        ]);
    }
}
