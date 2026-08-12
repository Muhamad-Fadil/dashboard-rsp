<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Laboratorium;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaboratoriumController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');

        $laboratorium = Laboratorium::with(['kunjungan.pasien', 'petugas'])
            ->when($cari, function ($query, $cari) {
                $query->where('jenis_pemeriksaan', 'like', "%{$cari}%")
                    ->orWhereHas('kunjungan.pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%")->orWhere('no_rm', 'like', "%{$cari}%"));
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('waktu_periksa')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'total' => Laboratorium::count(),
            'menunggu' => Laboratorium::where('status', 'menunggu')->count(),
            'diproses' => Laboratorium::where('status', 'diproses')->count(),
            'selesai' => Laboratorium::where('status', 'selesai')->count(),
        ];

        return view('divisi.layanan.laboratorium', [
            'division' => $division,
            'laboratorium' => $laboratorium,
            'ringkasan' => $ringkasan,
            'cari' => $cari,
            'status' => $status,
        ]);
    }
}