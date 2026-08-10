<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasienController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');

        $pasien = Pasien::with('jenisPembayaran')
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

        return view('divisi.pasien', [
            'division' => $division,
            'pasien' => $pasien,
            'cari' => $cari,
        ]);
    }
}