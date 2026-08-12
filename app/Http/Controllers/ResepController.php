<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResepController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');

        $resep = Resep::with(['kunjungan.pasien', 'dokter', 'detail.obat'])
            ->when($cari, function ($query, $cari) {
                $query->where('no_resep', 'like', "%{$cari}%")
                    ->orWhereHas('kunjungan.pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%")->orWhere('no_rm', 'like', "%{$cari}%"));
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('tanggal_resep')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'total' => Resep::count(),
            'menunggu' => Resep::where('status', 'menunggu')->count(),
            'diproses' => Resep::where('status', 'diproses')->count(),
            'selesai' => Resep::where('status', 'selesai')->count(),
        ];

        return view('divisi.layanan.resep', [
            'division' => $division,
            'resep' => $resep,
            'ringkasan' => $ringkasan,
            'cari' => $cari,
            'status' => $status,
        ]);
    }
}