<?php

namespace App\Services;

use App\Models\Anggaran;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use App\Models\Piutang;
use App\Models\RealisasiAnggaran;
use Illuminate\Support\Carbon;

class KeuanganIndikatorService
{
    /**
     * Total pendapatan dalam periode.
     */
    public function totalPendapatan(Carbon $awal, Carbon $akhir): float
    {
        return (float) Pendapatan::whereBetween('tanggal', [$awal, $akhir])->sum('jumlah');
    }

    /**
     * Pendapatan per kategori (rawat jalan, rawat inap, lab, dst) dalam periode.
     */
    public function pendapatanPerKategori(Carbon $awal, Carbon $akhir)
    {
        return Pendapatan::whereBetween('tanggal', [$awal, $akhir])
            ->join('kategori_pendapatan', 'pendapatan.kategori_pendapatan_id', '=', 'kategori_pendapatan.id')
            ->selectRaw('kategori_pendapatan.nama_kategori, sum(pendapatan.jumlah) as total')
            ->groupBy('kategori_pendapatan.nama_kategori')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Pendapatan per unit kerja dalam periode.
     */
    public function pendapatanPerUnit(Carbon $awal, Carbon $akhir)
    {
        return Pendapatan::whereBetween('tanggal', [$awal, $akhir])
            ->whereNotNull('unit_kerja_id')
            ->join('unit_kerja', 'pendapatan.unit_kerja_id', '=', 'unit_kerja.id')
            ->selectRaw('unit_kerja.nama_unit, sum(pendapatan.jumlah) as total')
            ->groupBy('unit_kerja.nama_unit')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Total belanja/pengeluaran dalam periode.
     */
    public function totalBelanja(Carbon $awal, Carbon $akhir): float
    {
        return (float) Pengeluaran::whereBetween('tanggal', [$awal, $akhir])->sum('jumlah');
    }

    /**
     * Belanja pegawai (khusus kategori "Belanja Pegawai") dalam periode.
     */
    public function belanjaPegawai(Carbon $awal, Carbon $akhir): float
    {
        return (float) Pengeluaran::whereBetween('tanggal', [$awal, $akhir])
            ->whereHas('kategori', fn ($q) => $q->where('kode', 'PGL-01'))
            ->sum('jumlah');
    }

    /**
     * Belanja operasional (khusus kategori "Belanja Operasional") dalam periode.
     */
    public function belanjaOperasional(Carbon $awal, Carbon $akhir): float
    {
        return (float) Pengeluaran::whereBetween('tanggal', [$awal, $akhir])
            ->whereHas('kategori', fn ($q) => $q->where('kode', 'PGL-03'))
            ->sum('jumlah');
    }

    /**
     * Realisasi anggaran per kategori pengeluaran dalam periode (tahun+bulan tertentu).
     * Return: collection [ ['kategori' => ..., 'anggaran' => ..., 'realisasi' => ..., 'persentase' => ...], ... ]
     */
    public function realisasiAnggaran(int $tahun, ?int $bulan = null)
    {
        $query = Anggaran::where('tahun', $tahun)->with('kategori');

        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        return $query->get()->map(function ($anggaran) {
            $realisasi = (float) RealisasiAnggaran::where('anggaran_id', $anggaran->id)->sum('jumlah_realisasi');
            $persentase = $anggaran->jumlah_anggaran > 0
                ? round(($realisasi / $anggaran->jumlah_anggaran) * 100, 1)
                : 0;

            return [
                'kategori' => $anggaran->kategori->nama_kategori ?? '-',
                'anggaran' => (float) $anggaran->jumlah_anggaran,
                'realisasi' => $realisasi,
                'persentase' => $persentase,
            ];
        });
    }

    /**
     * Total piutang yang belum terbayar penuh (sisa tagihan).
     */
    public function totalPiutang(): float
    {
        return (float) Piutang::whereIn('status', ['belum_lunas', 'sebagian'])
            ->get()
            ->sum(fn ($p) => $p->sisaPiutang());
    }

    /**
     * Tren pendapatan & belanja per bulan, untuk N bulan terakhir — dipakai buat grafik.
     * Return: collection [ ['bulan' => 'Januari 2026', 'pendapatan' => ..., 'belanja' => ...], ... ]
     */
    public function trenBulanan(int $jumlahBulan = 6)
    {
        $hasil = collect();

        for ($i = $jumlahBulan - 1; $i >= 0; $i--) {
            $bulanAcuan = now()->subMonths($i);
            $awal = $bulanAcuan->copy()->startOfMonth();
            $akhir = $bulanAcuan->copy()->endOfMonth()->min(now());

            $hasil->push([
                'bulan' => $bulanAcuan->translatedFormat('F Y'),
                'pendapatan' => $this->totalPendapatan($awal, $akhir),
                'belanja' => $this->totalBelanja($awal, $akhir),
            ]);
        }

        return $hasil;
    }

    /**
     * Ambil semua indikator sekaligus — dipanggil dari Controller.
     */
    public function ringkasan(Carbon $awal, Carbon $akhir): array
    {
        return [
            'total_pendapatan' => $this->totalPendapatan($awal, $akhir),
            'pendapatan_per_kategori' => $this->pendapatanPerKategori($awal, $akhir),
            'pendapatan_per_unit' => $this->pendapatanPerUnit($awal, $akhir),
            'total_belanja' => $this->totalBelanja($awal, $akhir),
            'belanja_pegawai' => $this->belanjaPegawai($awal, $akhir),
            'belanja_operasional' => $this->belanjaOperasional($awal, $akhir),
            'realisasi_anggaran' => $this->realisasiAnggaran($awal->year, $awal->month),
            'total_piutang' => $this->totalPiutang(),
            'tren_bulanan' => $this->trenBulanan(6),
        ];
    }
}