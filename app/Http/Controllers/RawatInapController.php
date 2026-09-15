<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Division;
use App\Models\Kamar;
use App\Models\RawatInap;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RawatInapController extends Controller
{
    public function index(Request $request, Division $division): View
    {
        abort_unless($division->slug === 'layanan', 404);

        $cari = $request->query('cari');
        $status = $request->query('status');
        $bangsal = $request->query('bangsal');

        $tanggalTerakhir = RawatInap::max('tanggal_masuk');
        $defaultAkhir = $tanggalTerakhir ? Carbon::parse($tanggalTerakhir)->endOfDay() : now()->endOfDay();
        $defaultAwal = $defaultAkhir->copy()->subDays(29)->startOfDay();

        $awal = $request->filled('awal') ? Carbon::parse($request->query('awal'))->startOfDay() : $defaultAwal;
        $akhir = $request->filled('akhir') ? Carbon::parse($request->query('akhir'))->endOfDay() : $defaultAkhir;

        $rawatInap = RawatInap::with(['kunjungan.pasien.jenisPembayaran', 'bed.kamar'])
            ->whereBetween('tanggal_masuk', [$awal, $akhir])
            ->when($cari, function ($query, $cari) {
                $query->whereHas('kunjungan', function ($qk) use ($cari) {
                    $qk->where('no_kunjungan', 'like', "%{$cari}%")
                        ->orWhereHas('pasien', fn ($qp) => $qp->where('nama', 'like', "%{$cari}%")->orWhere('no_rm', 'like', "%{$cari}%"));
                });
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($bangsal, function ($query, $bangsal) {
                $query->whereHas('bed.kamar', fn ($qk) => $qk->where('nama_bangsal', $bangsal));
            })
            ->orderByDesc('tanggal_masuk')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'sedang_dirawat' => RawatInap::where('tanggal_masuk', '<=', $akhir)
                ->where(fn ($q) => $q->whereNull('tanggal_keluar')->orWhere('tanggal_keluar', '>', $akhir))
                ->count(),
            'bed_terisi' => Bed::where('status', 'terisi')->count(),
            'bed_tersedia' => Bed::where('status', 'tersedia')->count(),
            'total_bed' => Bed::count(),
        ];

        $daftarBangsal = Kamar::select('nama_bangsal')->distinct()->orderBy('nama_bangsal')->pluck('nama_bangsal');

        return view('divisi.layanan.rawat-inap', compact('division', 'rawatInap', 'ringkasan', 'daftarBangsal', 'cari', 'status', 'bangsal', 'awal', 'akhir'));
    }
}