<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\PegawaiPelatihan;
use App\Models\Profesi;
use Illuminate\Support\Carbon;

class SdmIndikatorService
{
    /**
     * Total pegawai aktif.
     */
    public function totalPegawai(): int
    {
        return Pegawai::where('aktif', true)->count();
    }

    /**
     * Komposisi SDM per kategori profesi (medis, keperawatan, nakes_lain, nonkesehatan).
     * Return: collection [ ['kategori' => ..., 'label' => ..., 'total' => ..., 'persentase' => ...], ... ]
     */
    public function komposisiSdm()
    {
        $total = $this->totalPegawai();

        if ($total === 0) {
            return collect();
        }

        $label = [
            'medis' => 'Dokter',
            'keperawatan' => 'Perawat',
            'nakes_lain' => 'Tenaga Kesehatan Lain',
            'nonkesehatan' => 'Tenaga Administrasi/Pendukung',
        ];

        return Pegawai::where('pegawai.aktif', true)
            ->join('profesi', 'pegawai.profesi_id', '=', 'profesi.id')
            ->selectRaw('profesi.kategori, count(*) as total')
            ->groupBy('profesi.kategori')
            ->get()
            ->map(fn($row) => [
                'kategori' => $row->kategori,
                'label' => $label[$row->kategori] ?? $row->kategori,
                'total' => $row->total,
                'persentase' => round(($row->total / $total) * 100, 1),
            ]);
    }

    /**
     * Persentase kehadiran pegawai dalam periode.
     * Rumus: jumlah hadir / jumlah hari kerja wajib x 100%
     * "Hadir" di sini mencakup status hadir & terlambat (keduanya tetap masuk kerja).
     */
    public function persentaseKehadiran(Carbon $awal, Carbon $akhir): float
    {
        $totalAbsensi = Absensi::whereBetween('tanggal', [$awal, $akhir])->count();

        if ($totalAbsensi === 0) {
            return 0;
        }

        $hadir = Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->whereIn('status', ['hadir', 'terlambat'])
            ->count();

        return round(($hadir / $totalAbsensi) * 100, 1);
    }

    /**
     * Rekap kehadiran per status (hadir, terlambat, izin, sakit, alpha) dalam periode.
     */
    public function rekapStatusAbsensi(Carbon $awal, Carbon $akhir)
    {
        return Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');
    }

    /**
     * Jumlah pegawai yang sedang cuti/izin pada rentang tanggal tertentu (default: hari ini).
     */
    public function jumlahCutiAktif(?Carbon $tanggal = null): int
    {
        $tanggal = $tanggal ?? now();

        return \App\Models\Cuti::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->count();
    }

    /**
     * Distribusi pegawai per unit kerja.
     */
    public function distribusiPerUnit()
    {
        return Pegawai::where('aktif', true)
            ->join('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->selectRaw('unit_kerja.nama_unit, count(*) as total')
            ->groupBy('unit_kerja.nama_unit')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Jumlah pegawai yang mengikuti pelatihan dalam periode (berdasarkan tanggal pelatihan).
     */
    public function jumlahIkutPelatihan(Carbon $awal, Carbon $akhir): int
    {
        return PegawaiPelatihan::whereIn('status', ['selesai', 'mengikuti'])
            ->whereHas('pelatihan', function ($q) use ($awal, $akhir) {
                $q->whereBetween('tanggal_mulai', [$awal, $akhir]);
            })
            ->count();
    }

    public function produktivitasPerUnit(Carbon $awal, Carbon $akhir)
    {
        $pegawaiPerUnit = Pegawai::where('pegawai.aktif', true)
            ->join('unit_kerja', 'pegawai.unit_kerja_id', '=', 'unit_kerja.id')
            ->selectRaw('unit_kerja.id as unit_kerja_id, unit_kerja.nama_unit, count(*) as jumlah_pegawai')
            ->groupBy('unit_kerja.id', 'unit_kerja.nama_unit')
            ->get();

        $kunjunganPerUnit = \App\Models\Kunjungan::whereBetween('kunjungan.waktu_daftar', [$awal, $akhir])
            ->join('poli', 'kunjungan.poli_id', '=', 'poli.id')
            ->selectRaw('poli.unit_kerja_id, count(*) as beban_kerja')
            ->groupBy('poli.unit_kerja_id')
            ->get()
            ->pluck('beban_kerja', 'unit_kerja_id');

        return $pegawaiPerUnit->map(function ($unit) use ($kunjunganPerUnit) {
            $bebanKerja = (int) ($kunjunganPerUnit[$unit->unit_kerja_id] ?? 0);
            $rasio = $unit->jumlah_pegawai > 0
                ? round($bebanKerja / $unit->jumlah_pegawai, 1)
                : 0;

            return [
                'nama_unit' => $unit->nama_unit,
                'jumlah_pegawai' => $unit->jumlah_pegawai,
                'beban_kerja' => $bebanKerja,
                'rasio' => $rasio,
            ];
        })->sortByDesc('rasio')->values();
    }

    /**
     * Ambil semua indikator sekaligus — dipanggil dari Controller.
     */
    public function ringkasan(Carbon $awal, Carbon $akhir): array
    {
        return [
            'total_pegawai' => $this->totalPegawai(),
            'komposisi_sdm' => $this->komposisiSdm(),
            'persentase_kehadiran' => $this->persentaseKehadiran($awal, $akhir),
            'rekap_status_absensi' => $this->rekapStatusAbsensi($awal, $akhir),
            'jumlah_cuti_aktif' => $this->jumlahCutiAktif(),
            'distribusi_per_unit' => $this->distribusiPerUnit(),
            'jumlah_ikut_pelatihan' => $this->jumlahIkutPelatihan($awal, $akhir),
        ];
    }
}
