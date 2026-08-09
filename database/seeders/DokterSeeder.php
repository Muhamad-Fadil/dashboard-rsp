<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Poli;
use App\Models\Profesi;
use Illuminate\Database\Seeder;

class DokterSeeder extends Seeder
{
    public function run(): void
    {
        $dokterUmum = Profesi::where('nama_profesi', 'Dokter Umum')->first();
        $spAnak = Profesi::where('nama_profesi', 'Dokter Spesialis Anak')->first();
        $spKandungan = Profesi::where('nama_profesi', 'Dokter Spesialis Kandungan')->first();
        $spBedah = Profesi::where('nama_profesi', 'Dokter Spesialis Bedah')->first();
        $spPenyakitDalam = Profesi::where('nama_profesi', 'Dokter Spesialis Penyakit Dalam')->first();
        $spJantung = Profesi::where('nama_profesi', 'Dokter Spesialis Jantung')->first();
        $drGigi = Profesi::where('nama_profesi', 'Dokter Gigi')->first();

        $dokter = [
            ['nama' => 'dr. Andi Pratama', 'profesi_id' => $dokterUmum->id, 'poli_kode' => 'POLI-UM', 'no_str' => 'STR-001-2020'],
            ['nama' => 'dr. Siti Rahayu', 'profesi_id' => $dokterUmum->id, 'poli_kode' => 'POLI-UM', 'no_str' => 'STR-002-2019'],
            ['nama' => 'dr. Budi Santoso, Sp.A', 'profesi_id' => $spAnak->id, 'poli_kode' => 'POLI-AN', 'no_str' => 'STR-003-2018'],
            ['nama' => 'dr. Maria Fransisca, Sp.OG', 'profesi_id' => $spKandungan->id, 'poli_kode' => 'POLI-KA', 'no_str' => 'STR-004-2017'],
            ['nama' => 'dr. Hendra Wijaya, Sp.B', 'profesi_id' => $spBedah->id, 'poli_kode' => 'POLI-BE', 'no_str' => 'STR-005-2016'],
            ['nama' => 'dr. Dewi Lestari, Sp.PD', 'profesi_id' => $spPenyakitDalam->id, 'poli_kode' => 'POLI-PD', 'no_str' => 'STR-006-2015'],
            ['nama' => 'dr. Rudi Hartono, Sp.JP', 'profesi_id' => $spJantung->id, 'poli_kode' => 'POLI-JA', 'no_str' => 'STR-007-2019'],
            ['nama' => 'drg. Putri Ayu', 'profesi_id' => $drGigi->id, 'poli_kode' => 'POLI-GI', 'no_str' => 'STR-008-2021'],
            ['nama' => 'dr. Fajar Nugroho', 'profesi_id' => $dokterUmum->id, 'poli_kode' => 'POLI-UM', 'no_str' => 'STR-009-2020'],
            ['nama' => 'dr. Ratna Sari, Sp.A', 'profesi_id' => $spAnak->id, 'poli_kode' => 'POLI-AN', 'no_str' => 'STR-010-2018'],
        ];

        foreach ($dokter as $d) {
            $poli = Poli::where('kode_poli', $d['poli_kode'])->first();

            Dokter::updateOrCreate(
                ['no_str' => $d['no_str']],
                [
                    'nama' => $d['nama'],
                    'profesi_id' => $d['profesi_id'],
                    'poli_id' => $poli?->id,
                    'no_hp' => '0812' . rand(10000000, 99999999),
                    'aktif' => true,
                ]
            );
        }
    }
}