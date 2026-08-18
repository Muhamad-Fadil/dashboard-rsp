<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Kunjungan;
use App\Models\Laboratorium;
use App\Models\Operasi;
use App\Models\Pasien;
use App\Models\RawatInap;
use App\Models\Radiologi;
use App\Models\Resep;
use App\Services\KeuanganIndikatorService;
use App\Services\LayananIndikatorService;
use App\Services\SdmIndikatorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DivisiController extends Controller
{
    public function show(Request $request, Division $division): View
    {
        $awal = $request->filled('awal')
            ? \Illuminate\Support\Carbon::parse($request->query('awal'))
            : now()->subDays(30);

        $akhir = $request->filled('akhir')
            ? \Illuminate\Support\Carbon::parse($request->query('akhir'))
            : now();

        $jumlahBulanChart = (int) $request->query('bulan_chart', 6);

        return match ($division->slug) {
            'layanan' => $this->tampilLayanan($division, $awal, $akhir, $jumlahBulanChart),
            'sdm' => view('divisi.sdm.dashboard', [
                'division' => $division,
                'data' => app(SdmIndikatorService::class)->ringkasan($awal, $akhir),
                'awal' => $awal,
                'akhir' => $akhir,
            ]),
            'keuangan' => view('divisi.keuangan.dashboard', [
                'division' => $division,
                'data' => app(KeuanganIndikatorService::class)->ringkasan($awal, $akhir),
                'awal' => $awal,
                'akhir' => $akhir,
            ]),
            default => abort(404),
        };
    }

    protected function tampilLayanan(Division $division, $awal, $akhir, int $jumlahBulanChart): View
    {
        // Operator nggak boleh lihat Ringkasan (data gabungan semua sub-menu) — arahkan ke sub-menunya sendiri
        if (auth()->user()->role === 'operator') {
            abort(403, 'Operator tidak memiliki akses ke halaman Ringkasan. Hubungi Manajer divisi Anda.');
        }
        
        $service = app(LayananIndikatorService::class);

        $data = $service->ringkasan($awal, $akhir);
        $data['kunjungan_per_bulan'] = $service->kunjunganPerBulan($jumlahBulanChart);

        // ringkasan cepat dari tiap sub-menu, buat ditampilkan sebagai akses cepat di Ringkasan
        $ringkasanSubMenu = [
            'pasien' => [
                'total' => Pasien::count(),
            ],
            'kunjungan' => [
                'total' => Kunjungan::count(),
                'menunggu' => Kunjungan::where('status', 'menunggu')->count(),
            ],
            'rawat_inap' => [
                'dirawat' => RawatInap::whereNull('tanggal_keluar')->count(),
            ],
            'operasi' => [
                'aktif' => Operasi::whereIn('status', ['dijadwalkan', 'berlangsung'])->count(),
            ],
            'laboratorium' => [
                'aktif' => Laboratorium::whereIn('status', ['menunggu', 'diproses'])->count(),
            ],
            'radiologi' => [
                'aktif' => Radiologi::whereIn('status', ['menunggu', 'diproses'])->count(),
            ],
            'resep' => [
                'aktif' => Resep::whereIn('status', ['menunggu', 'diproses'])->count(),
            ],
        ];

        return view('divisi.layanan.dashboard', [
            'division' => $division,
            'data' => $data,
            'ringkasanSubMenu' => $ringkasanSubMenu,
            'awal' => $awal,
            'akhir' => $akhir,
            'jumlahBulanChart' => $jumlahBulanChart,
        ]);
    }

    /**
     * Endpoint AJAX: rincian kunjungan per hari untuk 1 bulan tertentu (drill-down grafik).
     */
    public function kunjunganHarian(Request $request, Division $division)
    {
        abort_unless($division->slug === 'layanan', 404);

        $tahun = (int) $request->query('tahun');
        $bulan = (int) $request->query('bulan');

        $data = app(LayananIndikatorService::class)->kunjunganPerHari($tahun, $bulan);

        return response()->json($data);
    }
}