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
     * Jumlah pasien yang SEDANG dirawat sekarang (belum keluar).
     */
    public function pasienRawatInapAktif(): int
    {
        return RawatInap::whereNull('tanggal_keluar')->count();
    }

    /**
     * Ketersediaan tempat tidur saat ini.
     */
    public function ketersediaanBed(): array
    {
        $total = Bed::count();
        $tersedia = Bed::where('status', 'tersedia')->count();

        return [
            'total' => $total,
            'tersedia' => $tersedia,
            'terisi' => $total - $tersedia,
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
     * Rata-rata waktu tunggu pelayanan dalam menit (dari daftar sampai mulai dilayani).
     */
    public function waktuTungguRataRata(Carbon $awal, Carbon $akhir): float
    {
        $kunjungan = Kunjungan::whereBetween('waktu_daftar', [$awal, $akhir])
            ->whereNotNull('waktu_dilayani')
            ->get();

        if ($kunjungan->isEmpty()) {
            return 0;
        }

        $totalMenit = $kunjungan->sum(fn ($k) => $k->waktuTungguMenit() ?? 0);

        return round($totalMenit / $kunjungan->count(), 1);
    }

    /**
     * Tren jumlah kunjungan per bulan, untuk N bulan terakhir — dipakai buat grafik.
     */
    public function kunjunganPerBulan(int $jumlahBulan = 6)
    {
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
     * Trend penyakit (diagnosa) terbanyak dalam periode — dipakai buat grafik/tabel trend penyakit.
    */

    public function trendPenyakit(Carbon $awal, Carbon $akhir, int $limit = 8)
    {
        return Kunjungan::whereBetween('waktu_daftar', [$awal, $akhir])
        ->whereNotNull('diagnosa')
        ->selectRaw('diagnosa, count(*) as total')
        ->groupBy('diagnosa')
        ->orderByDesc('total')
        ->limit($limit)
        ->get();
 }
        /**
         * Ambil semua indikator sekaligus dalam 1 array — ini yang dipanggil dari Controller.
         */
        
    public function ringkasan(Carbon $awal, Carbon $akhir): array
    {
        return [
            'jumlah_kunjungan' => $this->jumlahKunjungan($awal, $akhir),
            'kunjungan_per_poli' => $this->kunjunganPerPoli($awal, $akhir),
            'pasien_rawat_inap_aktif' => $this->pasienRawatInapAktif(),
            'ketersediaan_bed' => $this->ketersediaanBed(),
            'bor' => $this->bor($awal, $akhir),
            'alos' => $this->alos($awal, $akhir),
            'toi' => $this->toi($awal, $akhir),
            'bto' => $this->bto($awal, $akhir),
            'waktu_tunggu_rata_rata' => $this->waktuTungguRataRata($awal, $akhir),
            'kunjungan_per_bulan' => $this->kunjunganPerBulan(6),
            'trend_penyakit' => $this->trendPenyakit($awal, $akhir),
        ];
    }
}