<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = Division::where('slug', 'layanan')->first();
        $sdm = Division::where('slug', 'sdm')->first();
        $keuangan = Division::where('slug', 'keuangan')->first();

        $unitKerja = [
            // --- Divisi Layanan ---
            ['nama_unit' => 'Instalasi Gawat Darurat', 'kode_unit' => 'IGD', 'division_id' => $layanan->id, 'keterangan' => 'Pelayanan gawat darurat 24 jam'],
            ['nama_unit' => 'Poliklinik Rawat Jalan', 'kode_unit' => 'POLI', 'division_id' => $layanan->id, 'keterangan' => 'Induk seluruh poliklinik'],
            ['nama_unit' => 'Instalasi Rawat Inap', 'kode_unit' => 'RANAP', 'division_id' => $layanan->id, 'keterangan' => 'Ruang perawatan pasien inap'],
            ['nama_unit' => 'Instalasi Bedah Sentral', 'kode_unit' => 'IBS', 'division_id' => $layanan->id, 'keterangan' => 'Ruang operasi'],
            ['nama_unit' => 'Instalasi Farmasi', 'kode_unit' => 'FARM', 'division_id' => $layanan->id, 'keterangan' => 'Pengelolaan obat & resep'],
            ['nama_unit' => 'Instalasi Laboratorium', 'kode_unit' => 'LAB', 'division_id' => $layanan->id, 'keterangan' => 'Pemeriksaan laboratorium'],
            ['nama_unit' => 'Instalasi Radiologi', 'kode_unit' => 'RAD', 'division_id' => $layanan->id, 'keterangan' => 'Pemeriksaan radiologi'],

            // --- Divisi SDM ---
            ['nama_unit' => 'Bagian Kepegawaian', 'kode_unit' => 'SDM-01', 'division_id' => $sdm->id, 'keterangan' => 'Administrasi & data pegawai'],
            ['nama_unit' => 'Bagian Diklat', 'kode_unit' => 'SDM-02', 'division_id' => $sdm->id, 'keterangan' => 'Pendidikan & pelatihan pegawai'],

            // --- Divisi Keuangan ---
            ['nama_unit' => 'Bagian Keuangan', 'kode_unit' => 'KEU-01', 'division_id' => $keuangan->id, 'keterangan' => 'Pengelolaan pendapatan & belanja'],
            ['nama_unit' => 'Bagian Akuntansi & Anggaran', 'kode_unit' => 'KEU-02', 'division_id' => $keuangan->id, 'keterangan' => 'Penyusunan & realisasi anggaran'],
            ['nama_unit' => 'Bagian Penagihan & Klaim BPJS', 'kode_unit' => 'KEU-03', 'division_id' => $keuangan->id, 'keterangan' => 'Piutang pasien & klaim BPJS'],
        ];

        foreach ($unitKerja as $unit) {
            UnitKerja::updateOrCreate(['kode_unit' => $unit['kode_unit']], $unit);
        }
    }
}