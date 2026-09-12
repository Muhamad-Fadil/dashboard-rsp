<?php

namespace App\Console\Commands;

use App\Models\Bed;
use App\Models\Kamar;
use App\Models\KategoriPendapatan;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Pendapatan;
use App\Models\Poli;
use App\Models\RawatInap;
use App\Models\Referensi;
use App\Models\UnitKerja;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportPasienRs extends Command
{
    protected $signature = 'import:pasien-rs {file}';
    protected $description = 'Import data kunjungan pasien dari file SQL export RS (format: no_reg, no_mr, tgl_reg, tgl_pulang, nama_pas, umur, lp, ds_dep, ds_pastipe, inap, emg, total_bill)';

    protected array $pasienCache = [];
    protected array $poliCache = [];
    protected array $kamarBangsalCache = [];
    protected array $bedRoundRobin = [];
    protected array $jenisPembayaranCache = [];
    protected array $kategoriPendapatanCache = [];
    protected ?UnitKerja $unitPoli = null;

    protected array $mapBangsal = [
        'MAWAR' => 'Mawar Bawah',
        'TERATE' => 'Teratai',
        'KACA PIRING A' => 'Kacapiring',
        'KACA PIRING B' => 'Kacapiring',
        'MELATI' => 'Melati',
        'TANJUNG' => 'Tanjung',
        'ANGGREK ATAS' => 'Anggrek Atas',
        'ANGGREK BAWAH' => 'Anggrek Bawah',
    ];

    public function handle(): int
    {
        $path = $this->argument('file');

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return self::FAILURE;
        }

        $this->unitPoli = UnitKerja::where('kode_unit', 'POLI')->first();
        $this->muatCacheReferensi();

        $stat = ['pasien' => 0, 'kunjungan' => 0, 'rawat_inap' => 0, 'pendapatan' => 0, 'error' => 0];

        $handle = fopen($path, 'r');
        $baris = 0;
        $bar = null;

        DB::beginTransaction();

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (! str_starts_with($line, 'INSERT INTO')) {
                    continue;
                }

                $baris++;

                if (! preg_match_all("/'((?:[^'\\\\]|\\\\.)*)'/", $line, $matches)) {
                    $stat['error']++;
                    continue;
                }

                $nilai = $matches[1];
                if (count($nilai) < 12) {
                    $stat['error']++;
                    continue;
                }

                [$noReg, $noMr, $tglReg, $tglPulang, $namaPas, $umur, $lp, $dsDep, $dsPastipe, $inap, $emg, $totalBill] = $nilai;

                $tglReg = $this->parseTanggal($tglReg);
                $tglPulang = $this->parseTanggal($tglPulang);

                if (! $tglReg) {
                    $stat['error']++;
                    continue;
                }

                $pasien = $this->cariAtauBuatPasien($noMr, $namaPas, $umur, $lp, $dsPastipe, $tglReg, $stat);

                $jenisKunjungan = $this->tentukanJenisKunjungan($inap, $emg, $dsDep);
                $poliId = $this->cariAtauBuatPoli($dsDep);

                $noKunjungan = $this->pastikanUnikNoKunjungan($noReg, $baris);

                $kunjungan = Kunjungan::create([
                    'no_kunjungan' => $noKunjungan,
                    'pasien_id' => $pasien->id,
                    'poli_id' => $poliId,
                    'dokter_id' => null, // sumber data tidak punya info dokter
                    'jenis_kunjungan' => $jenisKunjungan,
                    'keluhan' => null,
                    'status' => $tglPulang ? 'selesai' : 'dilayani',
                    'waktu_daftar' => $tglReg,
                    'waktu_dilayani' => null, // sumber data tidak punya jam pelayanan
                    'waktu_selesai' => $tglPulang,
                    'user_id' => null,
                ]);
                $stat['kunjungan']++;

                if ($jenisKunjungan === 'rawat_inap') {
                    $this->buatRawatInap($kunjungan, $dsDep, $tglReg, $tglPulang, $stat);
                }

                $this->buatPendapatan($kunjungan, $inap, $totalBill, $tglReg, $stat);

                if ($baris % 500 === 0) {
                    $this->info("Diproses: {$baris} baris...");
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Import dibatalkan karena error: ' . $e->getMessage());
            return self::FAILURE;
        }

        fclose($handle);

        $this->newLine();
        $this->info('=== Import selesai ===');
        $this->table(['Jenis', 'Jumlah'], [
            ['Baris diproses', $baris],
            ['Pasien baru', $stat['pasien']],
            ['Kunjungan baru', $stat['kunjungan']],
            ['Rawat inap baru', $stat['rawat_inap']],
            ['Pendapatan baru', $stat['pendapatan']],
            ['Baris error/dilewati', $stat['error']],
        ]);

        return self::SUCCESS;
    }

    protected function muatCacheReferensi(): void
    {
        foreach (Referensi::where('kategori', 'jenis_pembayaran')->get() as $r) {
            $this->jenisPembayaranCache[$r->kode] = $r->id;
        }
        foreach (KategoriPendapatan::all() as $k) {
            $this->kategoriPendapatanCache[$k->kode] = $k->id;
        }
    }

    protected function parseTanggal(?string $nilai): ?Carbon
    {
        $nilai = trim($nilai ?? '');
        if ($nilai === '' || $nilai === '0000-00-00') {
            return null;
        }

        try {
            return Carbon::parse($nilai)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function tentukanJenisPembayaranId(string $dsPastipe): array
    {
        $dsPastipe = strtoupper(trim($dsPastipe));

        if ($dsPastipe === 'TUNAI') {
            return [$this->jenisPembayaranCache['tunai'] ?? null, null];
        }

        if (str_contains($dsPastipe, 'BPJS')) {
            return [$this->jenisPembayaranCache['bpjs'] ?? null, null];
        }

        // selain BPJS & Tunai -> Lain-lain, simpan keterangan aslinya
        return [$this->jenisPembayaranCache['lainnya'] ?? null, ucwords(strtolower($dsPastipe))];
    }

    protected function estimasiTanggalLahir(string $umur, Carbon $tglAcuan): ?string
    {
        if (! preg_match('/(\d+)\s*th\s*(\d+)\s*bln\s*(\d+)\s*hr/i', $umur, $m)) {
            return null;
        }

        return $tglAcuan->copy()->subYears((int) $m[1])->subMonths((int) $m[2])->subDays((int) $m[3])->format('Y-m-d');
    }

    protected function cariAtauBuatPasien(string $noMr, string $namaPas, string $umur, string $lp, string $dsPastipe, Carbon $tglReg, array &$stat): Pasien
    {
        if (isset($this->pasienCache[$noMr])) {
            return $this->pasienCache[$noMr];
        }

        $existing = Pasien::where('no_rm', $noMr)->first();
        if ($existing) {
            $this->pasienCache[$noMr] = $existing;
            return $existing;
        }

        [$jenisPembayaranId, $keterangan] = $this->tentukanJenisPembayaranId($dsPastipe);

        $pasien = Pasien::create([
            'no_rm' => $noMr,
            'no_registrasi' => null,
            'tanggal_registrasi' => $tglReg->format('Y-m-d'),
            'nama' => trim($namaPas),
            'jenis_kelamin' => strtoupper(trim($lp)) === 'P' ? 'P' : 'L',
            'tanggal_lahir' => $this->estimasiTanggalLahir($umur, $tglReg),
            'alamat' => null,
            'no_hp' => null,
            'nik' => null,
            'jenis_pembayaran_id' => $jenisPembayaranId,
            'keterangan_pembayaran' => $keterangan,
        ]);

        $this->pasienCache[$noMr] = $pasien;
        $stat['pasien']++;

        return $pasien;
    }

    protected function tentukanJenisKunjungan(string $inap, string $emg, string $dsDep): string
    {
        if (strtoupper(trim($inap)) === 'Y') {
            return 'rawat_inap';
        }

        if (strtoupper(trim($emg)) === 'Y' || str_contains(strtoupper($dsDep), 'GAWAT DARURAT')) {
            return 'igd';
        }

        return 'rawat_jalan';
    }

    protected function cariAtauBuatPoli(string $dsDep): ?int
    {
        $dsDep = trim($dsDep);

        if (! str_starts_with(strtoupper($dsDep), 'POLIKLINIK')) {
            return null;
        }

        if (isset($this->poliCache[$dsDep])) {
            return $this->poliCache[$dsDep];
        }

        $namaPoli = ucwords(strtolower($dsDep));
        $kodePoli = 'POLI-IMPORT-' . strtoupper(substr(md5($dsDep), 0, 6));

        $poli = Poli::firstOrCreate(
            ['nama_poli' => $namaPoli],
            ['kode_poli' => $kodePoli, 'unit_kerja_id' => $this->unitPoli?->id, 'aktif' => true]
        );

        $this->poliCache[$dsDep] = $poli->id;

        return $poli->id;
    }

    protected function buatRawatInap(Kunjungan $kunjungan, string $dsDep, Carbon $tglReg, ?Carbon $tglPulang, array &$stat): void
    {
        $dsDep = strtoupper(trim($dsDep));
        $namaBangsal = $this->mapBangsal[$dsDep] ?? null;

        $bedId = null;

        if ($namaBangsal) {
            if (! isset($this->kamarBangsalCache[$namaBangsal])) {
                $this->kamarBangsalCache[$namaBangsal] = Kamar::where('nama_bangsal', $namaBangsal)->pluck('id')->all();
                $this->bedRoundRobin[$namaBangsal] = 0;
            }

            $kamarIds = $this->kamarBangsalCache[$namaBangsal];

            if (! empty($kamarIds)) {
                $bedList = Bed::whereIn('kamar_id', $kamarIds)->pluck('id')->all();

                if (! empty($bedList)) {
                    $idx = $this->bedRoundRobin[$namaBangsal] % count($bedList);
                    $bedId = $bedList[$idx];
                    $this->bedRoundRobin[$namaBangsal]++;
                }
            }
        }

        if (! $bedId) {
            // fallback: kalau bangsal tidak kecocokan, pakai bed manapun (biar tidak gagal import)
            $bedId = Bed::inRandomOrder()->value('id');
        }

        RawatInap::create([
            'kunjungan_id' => $kunjungan->id,
            'bed_id' => $bedId,
            'dokter_id' => null,
            'tanggal_masuk' => $tglReg,
            'tanggal_keluar' => $tglPulang,
            'status' => $tglPulang ? 'pulang' : 'dirawat',
            'diagnosa' => null,
        ]);

        $stat['rawat_inap']++;
    }

    protected function buatPendapatan(Kunjungan $kunjungan, string $inap, string $totalBill, Carbon $tglReg, array &$stat): void
    {
        $kodeKategori = strtoupper(trim($inap)) === 'Y' ? 'PDT-02' : 'PDT-01';
        $kategoriId = $this->kategoriPendapatanCache[$kodeKategori] ?? null;

        if (! $kategoriId) {
            return;
        }

        Pendapatan::create([
            'kategori_pendapatan_id' => $kategoriId,
            'unit_kerja_id' => null,
            'kunjungan_id' => $kunjungan->id,
            'tanggal' => $tglReg,
            'jumlah' => (float) $totalBill,
            'keterangan' => 'Import data RS',
            'user_id' => null,
        ]);

        $stat['pendapatan']++;
    }

    protected function pastikanUnikNoKunjungan(string $noReg, int $baris): string
    {
        $kandidat = $noReg;

        if (Kunjungan::where('no_kunjungan', $kandidat)->exists()) {
            $kandidat = $noReg . '-' . $baris;
        }

        return $kandidat;
    }
}