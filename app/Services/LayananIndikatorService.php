<?php

namespace App\Services;

use App\Models\Bed;
use App\Models\Kunjungan;
use App\Models\Poli;
use App\Models\RawatInap;
use Illuminate\Support\Carbon;

class LayananIndikatorService
{
    /**
     * Jumlah total kunjungan (rawat jalan + rawat inap + igd) dalam periode.
     */
    public function jumlahKunjungan(Carbon $awal, Carbon $akhir): int
    {
        return Kunjungan::whereBetween('waktu_daftar', [$awal, $akhir])->count();
    }

    /**
     * Kunjungan per poliklinik dalam periode — buat tabel/grafik "kunjungan per poli".
     * Return: collection [ ['nama_poli' => ..., 'total' => ...], ... ]
     */
    public function kunjunganPerPoli(Carbon $awal, Carbon $akhir)
    {
        return Kunjungan::whereBetween('waktu_daftar', [$awal, $akhir])
            ->whereNotNull('poli_id')
            ->selectRaw('poli_id, count(*) as total')
            ->groupBy('poli_id')
            ->with('poli:id,nama_poli')
            ->get()
            ->map(fn ($row) => [
                'nama_poli' => $row->poli->nama_poli ?? '-',
                'total' => $row->total,
            ]);
    }


    /**
     * Jumlah pasien yang sedang dirawat PADA AKHIR periode yang dipilih (bukan selalu hari ini).
     */
    public function pasienRawatInapAktif(Carbon $akhir): int
    {
        return RawatInap::where('tanggal_masuk', '<=', $akhir)
            ->where(function ($q) use ($akhir) {
                $q->whereNull('tanggal_keluar')->orWhere('tanggal_keluar', '>', $akhir);
            })
            ->count();
    }

    /**
     * Ketersediaan tempat tidur saat ini.
     */
    /**
     * Ketersediaan bed PADA AKHIR periode yang dipilih, dihitung dari data rawat_inap
     * (bukan dari kolom status bed, karena itu cuma nyimpen kondisi hari ini).
     */
    public function ketersediaanBed(Carbon $akhir): array
    {
        $total = Bed::count();

        $terisi = RawatInap::where('tanggal_masuk', '<=', $akhir)
            ->where(function ($q) use ($akhir) {
                $q->whereNull('tanggal_keluar')->orWhere('tanggal_keluar', '>', $akhir);
            })
            ->distinct('bed_id')
            ->count('bed_id');

        return [
            'total' => $total,
            'tersedia' => $total - $terisi,
            'terisi' => $terisi,
        ];
    }

    /**
     * Total hari rawat dalam periode (dipakai bareng buat BOR & TOI).
     * Menghitung overlap tiap rawat_inap dengan rentang periode yang diminta.
     */
    protected function totalHariRawat(Carbon $awal, Carbon $akhir): float
    {
        $rawatInap = RawatInap::where('tanggal_masuk', '<=', $akhir)
            ->where(function ($q) use ($awal) {
                $q->whereNull('tanggal_keluar')->orWhere('tanggal_keluar', '>=', $awal);
            })
            ->get();

        $totalHari = 0;

        foreach ($rawatInap as $ri) {
            $mulaiOverlap = $ri->tanggal_masuk->max($awal);
            $selesaiOverlap = ($ri->tanggal_keluar ?? $akhir)->min($akhir);

            $hari = $mulaiOverlap->diffInDays($selesaiOverlap);
            $totalHari += max($hari, 0);
        }

        return $totalHari;
    }

    /**
     * BOR (Bed Occupancy Rate) dalam persen.
     * Rumus: (jumlah hari perawatan / (jumlah bed x jumlah hari periode)) x 100%
     */
    public function bor(Carbon $awal, Carbon $akhir): float
    {
        $jumlahBed = Bed::count();
        $jumlahHariPeriode = $awal->diffInDays($akhir) + 1;

        if ($jumlahBed === 0 || $jumlahHariPeriode === 0) {
            return 0;
        }

        $hariRawat = $this->totalHariRawat($awal, $akhir);

        return round(($hariRawat / ($jumlahBed * $jumlahHariPeriode)) * 100, 2);
    }

    /**
     * ALOS (Average Length of Stay) dalam hari.
     * Rumus: total lama rawat pasien KELUAR / jumlah pasien keluar
     */
    public function alos(Carbon $awal, Carbon $akhir): float
    {
        $pasienKeluar = RawatInap::whereBetween('tanggal_keluar', [$awal, $akhir])->get();

        if ($pasienKeluar->isEmpty()) {
            return 0;
        }

        $totalLamaRawat = $pasienKeluar->sum(fn ($ri) => $ri->lamaRawatHari() ?? 0);

        return round($totalLamaRawat / $pasienKeluar->count(), 2);
    }

    /**
     * TOI (Turn Over Interval) dalam hari.
     * Rumus: ((bed x hari periode) - hari rawat) / jumlah pasien keluar
     */
    public function toi(Carbon $awal, Carbon $akhir): float
    {
        $jumlahBed = Bed::count();
        $jumlahHariPeriode = $awal->diffInDays($akhir) + 1;
        $jumlahPasienKeluar = RawatInap::whereBetween('tanggal_keluar', [$awal, $akhir])->count();

        if ($jumlahPasienKeluar === 0) {
            return 0;
        }

        $hariRawat = $this->totalHariRawat($awal, $akhir);
        $toi = (($jumlahBed * $jumlahHariPeriode) - $hariRawat) / $jumlahPasienKeluar;

        return round(max($toi, 0), 2);
    }

    /**
     * BTO (Bed Turn Over) — frekuensi pemakaian tiap bed dalam periode.
     * Rumus: jumlah pasien keluar / jumlah bed
     */
    public function bto(Carbon $awal, Carbon $akhir): float
    {
        $jumlahBed = Bed::count();

        if ($jumlahBed === 0) {
            return 0;
        }

        $jumlahPasienKeluar = RawatInap::whereBetween('tanggal_keluar', [$awal, $akhir])->count();

        return round($jumlahPasienKeluar / $jumlahBed, 2);
    }

    /**
     * Tren jumlah kunjungan per bulan yang benar-benar ada di database,
     * melalui tanggal minimum dan maksimum kunjungan.
     */
    public function kunjunganPerBulan(int $jumlahBulan = 3)
    {
        $minTanggal = Kunjungan::min('waktu_daftar');
        $maxTanggal = Kunjungan::max('waktu_daftar');

        if ($minTanggal && $maxTanggal) {
            $bulanMulai = Carbon::parse($minTanggal)->startOfMonth();
            $bulanAkhir = Carbon::parse($maxTanggal)->startOfMonth();

            $hasil = collect();
            $bulan = $bulanMulai->copy();

            while ($bulan->lte($bulanAkhir)) {
                $awal = $bulan->copy()->startOfMonth();
                $akhir = $bulan->copy()->endOfMonth();

                $hasil->push([
                    'bulan' => $bulan->translatedFormat('F Y'),
                    'tahun_angka' => $bulan->year,
                    'bulan_angka' => $bulan->month,
                    'total' => Kunjungan::whereBetween('waktu_daftar', [$awal, $akhir])->count(),
                ]);

                $bulan->addMonth();
            }

            return $hasil;
        }

        // fallback aman bila data belum ada
        $hasil = collect();
        for ($i = $jumlahBulan - 1; $i >= 0; $i--) {
            $bulanAcuan = now()->subMonths($i);
            $awal = $bulanAcuan->copy()->startOfMonth();
            $akhir = $bulanAcuan->copy()->endOfMonth()->min(now());

            $hasil->push([
                'bulan' => $bulanAcuan->translatedFormat('F Y'),
                'tahun_angka' => $bulanAcuan->year,
                'bulan_angka' => $bulanAcuan->month,
                'total' => $this->jumlahKunjungan($awal, $akhir),
            ]);
        }

        return $hasil;
    }

    /**
     * Rincian jumlah kunjungan per hari, untuk 1 bulan tertentu — dipakai saat drill-down grafik.
     */
    public function kunjunganPerHari(int $tahun, int $bulan)
    {
        $awal = \Illuminate\Support\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhir = $awal->copy()->endOfMonth()->min(now());

        $hasil = collect();
        $tanggal = $awal->copy();

        while ($tanggal->lte($akhir)) {
            $hasil->push([
                'tanggal' => $tanggal->format('d M'),
                'total' => Kunjungan::whereDate('waktu_daftar', $tanggal->format('Y-m-d'))->count(),
            ]);
            $tanggal->addDay();
        }

        return $hasil;
    }

    
        /**
     * Breakdown status kunjungan dalam periode: berapa yang selesai berobat vs batal.
     */
    public function statusKunjungan(Carbon $awal, Carbon $akhir): array
    {
        $query = Kunjungan::whereBetween('waktu_daftar', [$awal, $akhir]);

        return [
            'selesai' => (clone $query)->where('status', 'selesai')->count(),
            'batal' => (clone $query)->where('status', 'batal')->count(),
        ];
    }

    /**
     * Trend kunjungan harian untuk 5 poliklinik paling sibuk, 30 hari terakhir (tetap, tidak ikut filter periode dashboard).
     */
    public function trendPoliklinikHarian(int $limit = 5)
    {
        $tanggalTerakhirData = Kunjungan::max('waktu_daftar');
        $akhir = $tanggalTerakhirData ? Carbon::parse($tanggalTerakhirData)->endOfDay() : now()->endOfDay();
        $awal = $akhir->copy()->subDays(29)->startOfDay();

        $topPoliIds = Kunjungan::whereBetween('waktu_daftar', [$awal, $akhir])
            ->whereNotNull('poli_id')
            ->selectRaw('poli_id, count(*) as total')
            ->groupBy('poli_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('poli_id');

        $labelTanggal = [];
        $dataPerPoli = [];

        foreach (Poli::whereIn('id', $topPoliIds)->get() as $poli) {
            $dataPerPoli[$poli->nama_poli] = [];
        }

        $tanggal = $awal->copy();
        while ($tanggal->lte($akhir)) {
            $labelTanggal[] = $tanggal->format('d M');

            foreach ($topPoliIds as $poliId) {
                $namaPoli = Poli::find($poliId)->nama_poli;
                $dataPerPoli[$namaPoli][] = Kunjungan::where('poli_id', $poliId)
                    ->whereDate('waktu_daftar', $tanggal->format('Y-m-d'))
                    ->count();
            }

            $tanggal->addDay();
        }

        return [
            'labels' => $labelTanggal,
            'series' => $dataPerPoli,
        ];
    }    
        
    public function ringkasan(Carbon $awal, Carbon $akhir): array
    {
        return [
            'jumlah_kunjungan' => $this->jumlahKunjungan($awal, $akhir),
            'kunjungan_per_poli' => $this->kunjunganPerPoli($awal, $akhir),
            'pasien_rawat_inap_aktif' => $this->pasienRawatInapAktif($akhir),
            'ketersediaan_bed' => $this->ketersediaanBed($akhir),
            'bor' => $this->bor($awal, $akhir),
            'alos' => $this->alos($awal, $akhir),
            'toi' => $this->toi($awal, $akhir),
            'bto' => $this->bto($awal, $akhir),
            'kunjungan_per_bulan' => $this->kunjunganPerBulan(3),
            'status_kunjungan' => $this->statusKunjungan($awal, $akhir),
            'trend_poliklinik_harian' => $this->trendPoliklinikHarian(5),
        ];
    }
}