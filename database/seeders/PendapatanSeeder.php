<?php

namespace Database\Seeders;

use App\Models\KategoriPendapatan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendapatanSeeder extends Seeder
{
    /**
     * Sumber data: dump SQL billing pasien asli (sudah dimasking pihak RS)
     * periode April-Juni 2026. Taruh file itu di:
     * database/seeders/data/pas_apr_jun_26_masking.sql
     *
     * Tiap baris INSERT dipetakan ke kategori & unit kerja:
     * - inap = 'Y'                              -> Rawat Inap (PDT-02) / unit RANAP
     * - inap = 'N' & departemen = IGD           -> Gawat Darurat (PDT-08) / unit IGD
     * - inap = 'N' & departemen mulai POLIKLINIK -> Rawat Jalan (PDT-01) / unit POLI
     *
     * Tabel milik divisi Layanan/SDM tidak disentuh sama sekali oleh seeder ini.
     */
    public function run(): void
    {
        $sqlPath = database_path('seeders/data/pas_apr_jun_26_masking.sql');

        if (! file_exists($sqlPath)) {
            $this->command?->warn("File data mentah tidak ditemukan: {$sqlPath}");
            return;
        }

        $operatorId = User::where('email', 'operator.keuangan@rspgoenawan.co.id')->first()?->id;

        $kategoriId = KategoriPendapatan::pluck('id', 'kode');
        $unitId = UnitKerja::pluck('id', 'kode_unit');

        $kategoriRawatJalan   = $kategoriId['PDT-01'] ?? null;
        $kategoriRawatInap    = $kategoriId['PDT-02'] ?? null;
        $kategoriGawatDarurat = $kategoriId['PDT-08'] ?? null;

        if (! $kategoriRawatJalan || ! $kategoriRawatInap || ! $kategoriGawatDarurat) {
            $this->command?->warn('Kategori PDT-01/PDT-02/PDT-08 belum lengkap. Jalankan KategoriPendapatanSeeder dulu.');
            return;
        }

        $unitPoli  = $unitId['POLI']  ?? null;
        $unitRanap = $unitId['RANAP'] ?? null;
        $unitIgd   = $unitId['IGD']   ?? null;

        // Hapus data pendapatan dummy lama. Tabel lain tidak disentuh.
        DB::table('pendapatan')->delete();

        $pattern = '/VALUES \(\'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\', \'([^\']*)\'\)/';

        $handle = fopen($sqlPath, 'r');
        $now = now();
        $batch = [];
        $batchSize = 500;
        $totalInsert = 0;

        while (($line = fgets($handle)) !== false) {
            if (! preg_match($pattern, $line, $m)) {
                continue;
            }

            // urutan kolom: no_reg, no_mr, tgl_reg, tgl_pulang, nama_pas, umur, lp, ds_dep, ds_pastipe, inap, emg, total_bill
            $noReg      = $m[1];
            $tglReg     = $m[3];
            $dsDep      = $m[8];
            $dsPastipe  = $m[9];
            $inap       = $m[10];
            $totalBill  = $m[12];

            if ($inap === 'Y') {
                $kategoriPendapatanId = $kategoriRawatInap;
                $unitKerjaId = $unitRanap;
                $labelJenis = 'Rawat Inap';
            } elseif ($dsDep === 'INSTALASI GAWAT DARURAT') {
                $kategoriPendapatanId = $kategoriGawatDarurat;
                $unitKerjaId = $unitIgd;
                $labelJenis = 'Gawat Darurat';
            } else {
                $kategoriPendapatanId = $kategoriRawatJalan;
                $unitKerjaId = $unitPoli;
                $labelJenis = 'Rawat Jalan';
            }

            $batch[] = [
                 'kategori_pendapatan_id' => $kategoriPendapatanId,
                 'unit_kerja_id' => $unitKerjaId,
                'kunjungan_id' => null,
                'ds_dep' => $dsDep,
                 'tanggal' => $tglReg,
                  'jumlah' => $totalBill,
                    'keterangan' => "Pendapatan {$labelJenis} - {$dsDep} ({$dsPastipe}) - No. Reg: {$noReg}",
                    'user_id' => $operatorId,
                    'created_at' => $now,
                    'updated_at' => $now,
            ];

            if (count($batch) >= $batchSize) {
                DB::table('pendapatan')->insert($batch);
                $totalInsert += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('pendapatan')->insert($batch);
            $totalInsert += count($batch);
        }

        fclose($handle);

        $this->command?->info("PendapatanSeeder: {$totalInsert} baris data pendapatan asli berhasil dimasukkan.");
    }
}