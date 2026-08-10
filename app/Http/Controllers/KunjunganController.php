<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KunjunganController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');
        $jenis = $request->query('jenis');
        $status = $request->query('status');
        $awal = $request->filled('awal') ? \Illuminate\Support\Carbon::parse($request->query('awal')) : now()->subDays(30);
        $akhir = $request->filled('akhir') ? \Illuminate\Support\Carbon::parse($request->query('akhir')) : now();

        $kunjungan = Kunjungan::with(['pasien', 'poli', 'dokter', 'operator'])
            ->whereBetween('waktu_daftar', [$awal->startOfDay(), $akhir->endOfDay()])
            ->when($cari, function ($query, $cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('no_kunjungan', 'like', "%{$cari}%")
                        ->orWhereHas('pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%")->orWhere('no_rm', 'like', "%{$cari}%"));
                });
            })
            ->when($jenis, fn ($query, $jenis) => $query->where('jenis_kunjungan', $jenis))
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('waktu_daftar')
            ->paginate(15)
            ->withQueryString();

        // ringkasan kecil buat kartu statistik di atas tabel
        $ringkasan = [
            'total' => Kunjungan::whereBetween('waktu_daftar', [$awal->startOfDay(), $akhir->endOfDay()])->count(),
            'menunggu' => Kunjungan::whereBetween('waktu_daftar', [$awal->startOfDay(), $akhir->endOfDay()])->where('status', 'menunggu')->count(),
            'selesai' => Kunjungan::whereBetween('waktu_daftar', [$awal->startOfDay(), $akhir->endOfDay()])->where('status', 'selesai')->count(),
            'igd' => Kunjungan::whereBetween('waktu_daftar', [$awal->startOfDay(), $akhir->endOfDay()])->where('jenis_kunjungan', 'igd')->count(),
        ];

        return view('divisi.kunjungan', [
            'division' => $division,
            'kunjungan' => $kunjungan,
            'ringkasan' => $ringkasan,
            'cari' => $cari,
            'jenis' => $jenis,
            'status' => $status,
            'awal' => $awal,
            'akhir' => $akhir,
        ]);
    }
}