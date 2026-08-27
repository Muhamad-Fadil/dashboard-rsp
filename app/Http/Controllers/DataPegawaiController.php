<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\SdmIndikatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataPegawaiController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'sdm', 404);

        $cari = $request->query('cari');

        return view('divisi.sdm.data-pegawai', [
            'division' => $division,
            'pegawai' => app(SdmIndikatorService::class)->daftarLengkapPegawai($cari),
            'cari' => $cari,
        ]);
    }
}
