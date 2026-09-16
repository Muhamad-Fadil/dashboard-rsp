<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportKlaimBpjsSql extends Command
{
    /**
     * php artisan klaim-bpjs:import storage/app/imports/pas_apr_jun_26_masking.sql
     * php artisan klaim-bpjs:import storage/app/imports/pas_apr_jun_26_masking.sql --fresh
     */
    protected $signature = 'klaim-bpjs:import {path} {--fresh : Hapus semua data klaim BPJS lama sebelum import}';

    protected $description = 'Import data Klaim BPJS dari file export SQL kunjungan pasien (kunjungan dengan cara bayar BPJS)';

    // ds_pastipe asli di data sumber -> label jenis BPJS yang ditampilkan
    private array $carabayarBpjs = [
        'BPJS' => 'BPJS',
        'BPJS PBI' => 'BPJS PBI',
        'BPJS NON PBI' => 'BPJS Non PBI',
        'ASKES SOSIAL' => 'Askes Sosial',
        'JAMKESMAS' => 'Jamkesmas',
    ];

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Menghapus semua data klaim BPJS lama...');
            DB::table('klaim_bpjs')->truncate();
        }

        $pattern = "/VALUES \('([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)'\)/";

        $batch = [];
        $totalMasuk = 0;
        $rekapJenis = [];
        $now = now();

        $handle = fopen($path, 'r');

        while (($baris = fgets($handle)) !== false) {
            if (! str_contains($baris, 'INSERT INTO')) {
                continue;
            }

            if (! preg_match($pattern, $baris, $m)) {
                continue;
            }

            [, $noReg, , , $tglPulang, , , , $dep, $pastipe, , , $totalBill] = $m;

            $pastipeUpper = strtoupper(trim($pastipe));

            // cuma ambil yang cara bayarnya termasuk keluarga BPJS -- selain itu dilewati
            if (! isset($this->carabayarBpjs[$pastipeUpper])) {
                continue;
            }

            $jenisBpjs = $this->carabayarBpjs[$pastipeUpper];
            $rekapJenis[$jenisBpjs] = ($rekapJenis[$jenisBpjs] ?? 0) + 1;

            $batch[] = [
                'pasien_id' => null,
                'kunjungan_id' => null,
                'no_reg' => $noReg,
                'jenis_bpjs' => $jenisBpjs,
                'no_sep' => 'SEP-'.str_replace('-', '', substr($tglPulang, 0, 10)).'-'.$noReg,
                'jumlah_klaim' => (float) $totalBill,
                // belum ada data hasil verifikasi BPJS di file sumber -- dikosongkan, bukan ditebak
                'jumlah_disetujui' => null,
                'tanggal_pengajuan' => $tglPulang,
                'tanggal_disetujui' => null,
                'status' => 'diajukan',
                'keterangan' => 'Klaim BPJS - '.ucwords(strtolower($dep)),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $totalMasuk++;

            if (count($batch) >= 1000) {
                DB::table('klaim_bpjs')->insert($batch);
                $batch = [];
                $this->info("Sudah diimport: {$totalMasuk} baris...");
            }
        }

        if (! empty($batch)) {
            DB::table('klaim_bpjs')->insert($batch);
        }

        fclose($handle);

        $this->newLine();
        $this->info("Selesai. Berhasil diimport: {$totalMasuk} baris klaim BPJS.");

        $this->newLine();
        $this->line('Rincian per jenis BPJS:');
        foreach ($rekapJenis as $jenis => $jumlah) {
            $this->line("  - {$jenis}: {$jumlah} klaim");
        }

        $this->newLine();
        $this->warn('Catatan: kolom "Disetujui" dikosongkan (status "diajukan") karena file sumber cuma berisi data tagihan, bukan hasil verifikasi BPJS.');

        return self::SUCCESS;
    }
}