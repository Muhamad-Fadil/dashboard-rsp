<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Pegawai;
use App\Models\PegawaiPelatihan;
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
     * Klasifikasi 1 profesi ke salah satu dari 5 kelompok SDM (dokter, perawat, nakes_lain,
     * administrasi, pendukung). Dipakai bareng oleh komposisiSdm() dan daftarPegawai() biar
     * logic pengelompokannya konsisten di satu tempat.
     */
    protected function klasifikasiKelompok(string $kategoriProfesi, string $namaProfesi): string
    {
        return match ($kategoriProfesi) {
            'medis' => 'dokter',
            'keperawatan' => 'perawat',
            'nakes_lain' => 'nakes_lain',
            default => str_contains(strtolower($namaProfesi), 'admin')
                ? 'administrasi'
                : 'pendukung',
        };
    }

    protected function labelKelompok(): array
    {
        return [
            'dokter' => 'Dokter',
            'perawat' => 'Perawat',
            'nakes_lain' => 'Tenaga Kesehatan Lain',
            'administrasi' => 'Tenaga Administrasi',
            'pendukung' => 'Tenaga Pendukung',
        ];
    }

    /**
     * Komposisi SDM per kelompok: Dokter, Perawat, Tenaga Kesehatan Lain, Tenaga Administrasi,
     * Tenaga Pendukung. 5 kelompok ini dipetakan dari 4 kategori profesi yang sudah ada di database
     * (medis, keperawatan, nakes_lain, nonkesehatan) — kategori "nonkesehatan" dipecah jadi
     * "administrasi" vs "pendukung" berdasarkan nama profesinya, TANPA ubah skema/tabel.
     * Return: collection [ ['kelompok' => ..., 'label' => ..., 'total' => ..., 'persentase' => ...], ... ]
     * Diurutkan dari yang jumlahnya paling besar.
     */
    public function komposisiSdm()
    {
        $total = $this->totalPegawai();

        if ($total === 0) {
            return collect();
        }

        $label = $this->labelKelompok();

        $pegawai = Pegawai::where('pegawai.aktif', true)
            ->join('profesi', 'pegawai.profesi_id', '=', 'profesi.id')
            ->selectRaw('profesi.kategori, profesi.nama_profesi')
            ->get();

        $kelompokTerhitung = $pegawai->countBy(
            fn($p) => $this->klasifikasiKelompok($p->kategori, $p->nama_profesi)
        );

        return $kelompokTerhitung
            ->map(fn($jumlah, $kelompok) => [
                'kelompok' => $kelompok,
                'label' => $label[$kelompok] ?? ucfirst($kelompok),
                'total' => $jumlah,
                'persentase' => round(($jumlah / $total) * 100, 1),
            ])
            ->sortByDesc('total')
            ->values();
    }

    /**
     * Daftar pegawai aktif lengkap (dipakai di sub-menu "Komposisi Pegawai"), dengan kelompok
     * SDM-nya masing-masing, bisa difilter per kelompok dan/atau kata kunci nama/NIP.
     */
    public function daftarPegawai(?string $kelompok = null, ?string $cari = null)
    {
        $label = $this->labelKelompok();

        $query = Pegawai::where('pegawai.aktif', true)
            ->with(['profesi', 'unitKerja']);

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('nip', 'like', "%{$cari}%");
            });
        }

        return $query->get()
            ->map(function ($p) use ($label) {
                $kelompokPegawai = $this->klasifikasiKelompok($p->profesi->kategori, $p->profesi->nama_profesi);
                $p->kelompok = $kelompokPegawai;
                $p->kelompok_label = $label[$kelompokPegawai] ?? ucfirst($kelompokPegawai);
                return $p;
            })
            ->when($kelompok, fn($collection) => $collection->where('kelompok', $kelompok))
            ->sortBy('nama')
            ->values();
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
     * Jumlah pegawai yang cuti/izin dalam rentang tanggal filter (bukan cuma snapshot hari ini).
     * Gabungan dari:
     * - Cuti yang disetujui & jadwalnya overlap dengan periode filter (tabel Cuti)
     * - Record absensi berstatus 'izin' dalam periode filter (tabel Absensi)
     */
    public function jumlahCutiAktif(Carbon $awal, Carbon $akhir): int
    {
        $jumlahCuti = \App\Models\Cuti::where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $akhir)
            ->whereDate('tanggal_selesai', '>=', $awal)
            ->count();

        $jumlahIzin = Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->where('status', 'izin')
            ->count();

        return $jumlahCuti + $jumlahIzin;
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

    /**
     * Target kehadiran yang ditetapkan rumah sakit (persen). Dipakai buat bandingkan
     * dengan realisasi kehadiran tiap bulan.
     */
    protected function targetKehadiran(): float
    {
        return 95.0;
    }

    /**
     * Realisasi kehadiran per bulan (N bulan terakhir) dibandingkan dengan target,
     * plus status: "Baik" (di atas target), "Sesuai target" (pas target), atau
     * "Perlu perhatian" (di bawah target). Dipakai buat tabel indikator kehadiran bulanan.
     */
    public function kehadiranBulanan(int $jumlahBulan = 5)
    {
        $target = $this->targetKehadiran();
        $hasil = collect();

        for ($i = $jumlahBulan - 1; $i >= 0; $i--) {
            $bulanAcuan = now()->subMonths($i);
            $awalBulan = $bulanAcuan->copy()->startOfMonth();
            $akhirBulan = $bulanAcuan->copy()->endOfMonth()->min(now());

            $realisasi = $this->persentaseKehadiran($awalBulan, $akhirBulan);

            $status = match (true) {
                $realisasi > $target => 'Baik',
                $realisasi < $target => 'Perlu perhatian',
                default => 'Sesuai target',
            };

            $hasil->push([
                'bulan' => $bulanAcuan->translatedFormat('F Y'),
                'target' => $target,
                'realisasi' => $realisasi,
                'status' => $status,
            ]);
        }

        return $hasil;
    }

    /**
     * Daftar record absensi dalam periode (dipakai di sub-menu "Kehadiran"), dengan info pegawai.
     */
    public function daftarAbsensi(Carbon $awal, Carbon $akhir, ?string $status = null)
    {
        $query = Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->with('pegawai')
            ->orderByDesc('tanggal');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Daftar pengajuan cuti dalam periode (dipakai di sub-menu "Cuti & Izin"), dengan info pegawai.
     */
    public function daftarCuti(Carbon $awal, Carbon $akhir, ?string $status = null)
    {
        $query = \App\Models\Cuti::where(function ($q) use ($awal, $akhir) {
            $q->whereDate('tanggal_mulai', '<=', $akhir)
                ->whereDate('tanggal_selesai', '>=', $awal);
        })
            ->with('pegawai')
            ->orderByDesc('tanggal_mulai');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    /**
     * Daftar record izin harian (bukan cuti terjadwal) dalam periode, dari tabel absensi.
     */
    public function daftarIzinHarian(Carbon $awal, Carbon $akhir)
    {
        return Absensi::whereBetween('tanggal', [$awal, $akhir])
            ->where('status', 'izin')
            ->with('pegawai')
            ->orderByDesc('tanggal')
            ->get();
    }

    /**
     * Daftar pelatihan dalam periode berikut jumlah pesertanya (dipakai di sub-menu "Pelatihan").
     */
    public function daftarPelatihan(Carbon $awal, Carbon $akhir)
    {
        return \App\Models\Pelatihan::whereBetween('tanggal_mulai', [$awal, $akhir])
            ->withCount([
                'peserta as jumlah_peserta' => fn($q) => $q->whereIn('status', ['selesai', 'mengikuti']),
                'peserta as jumlah_selesai' => fn($q) => $q->where('status', 'selesai'),
            ])
            ->orderByDesc('tanggal_mulai')
            ->get();
    }

    /**
     * Distribusi pegawai per unit kerja, LENGKAP dengan daftar nama pegawainya masing-masing
     * (dipakai di sub-menu "Distribusi Pegawai"). Beda dengan distribusiPerUnit() yang cuma
     * hitungan ringkas buat kartu di halaman Ringkasan.
     */
    public function distribusiPerUnitDetail()
    {
        return Pegawai::where('aktif', true)
            ->with(['profesi', 'unitKerja'])
            ->get()
            ->groupBy(fn($p) => $p->unitKerja->nama_unit ?? 'Tanpa Unit')
            ->map(fn($group, $namaUnit) => [
                'nama_unit' => $namaUnit,
                'total' => $group->count(),
                'pegawai' => $group->sortBy('nama')->values(),
            ])
            ->sortByDesc('total')
            ->values();
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
            'jumlah_cuti_aktif' => $this->jumlahCutiAktif($awal, $akhir),
            'distribusi_per_unit' => $this->distribusiPerUnit(),
            'jumlah_ikut_pelatihan' => $this->jumlahIkutPelatihan($awal, $akhir),
            'kehadiran_bulanan' => $this->kehadiranBulanan(5),
        ];
    }
}
