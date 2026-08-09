<?php

namespace Database\Seeders;

use App\Models\Poli;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class PoliSeeder extends Seeder
{
    public function run(): void
    {
        $unitPoli = UnitKerja::where('kode_unit', 'POLI')->first();

        $poli = [
            ['nama_poli' => 'Poli Umum', 'kode_poli' => 'POLI-UM'],
            ['nama_poli' => 'Poli Anak', 'kode_poli' => 'POLI-AN'],
            ['nama_poli' => 'Poli Gigi', 'kode_poli' => 'POLI-GI'],
            ['nama_poli' => 'Poli Kandungan', 'kode_poli' => 'POLI-KA'],
            ['nama_poli' => 'Poli Penyakit Dalam', 'kode_poli' => 'POLI-PD'],
            ['nama_poli' => 'Poli Bedah', 'kode_poli' => 'POLI-BE'],
            ['nama_poli' => 'Poli Mata', 'kode_poli' => 'POLI-MA'],
            ['nama_poli' => 'Poli THT', 'kode_poli' => 'POLI-TH'],
            ['nama_poli' => 'Poli Jantung', 'kode_poli' => 'POLI-JA'],
            ['nama_poli' => 'Poli Saraf', 'kode_poli' => 'POLI-SA'],
        ];

        foreach ($poli as $p) {
            Poli::updateOrCreate(
                ['kode_poli' => $p['kode_poli']],
                [
                    'nama_poli' => $p['nama_poli'],
                    'unit_kerja_id' => $unitPoli->id,
                    'aktif' => true,
                ]
            );
        }
    }
}