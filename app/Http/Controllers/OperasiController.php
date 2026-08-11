<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Operasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperasiController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');

        $operasi = Operasi::with(['kunjungan.pasien', 'dokter'])
            ->when($cari, function ($query, $cari) {
                $query->where('jenis_operasi', 'like', "%{$cari}%")
                    ->orWhereHas('kunjungan.pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%")->orWhere('no_rm', 'like', "%{$cari}%"));
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('waktu_mulai')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'total' => Operasi::count(),
            'dijadwalkan' => Operasi::where('status', 'dijadwalkan')->count(),
            'berlangsung' => Operasi::where('status', 'berlangsung')->count(),
            'selesai' => Operasi::where('status', 'selesai')->count(),
        ];

        return view('divisi.operasi', [
            'division' => $division,
            'operasi' => $operasi,
            'ringkasan' => $ringkasan,
            'cari' => $cari,
            'status' => $status,
        ]);
    }
}