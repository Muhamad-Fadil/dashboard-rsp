<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        $unitRanap = UnitKerja::where('kode_unit', 'RANAP')->first();

        $kamar = [
            ['nomor_kamar' => '101', 'kelas' => 'vip', 'tarif' => 750000],
            ['nomor_kamar' => '102', 'kelas' => 'vip', 'tarif' => 750000],
            ['nomor_kamar' => '201', 'kelas' => 'kelas_1', 'tarif' => 450000],
            ['nomor_kamar' => '202', 'kelas' => 'kelas_1', 'tarif' => 450000],
            ['nomor_kamar' => '203', 'kelas' => 'kelas_1', 'tarif' => 450000],
            ['nomor_kamar' => '301', 'kelas' => 'kelas_2', 'tarif' => 300000],
            ['nomor_kamar' => '302', 'kelas' => 'kelas_2', 'tarif' => 300000],
            ['nomor_kamar' => '401', 'kelas' => 'kelas_3', 'tarif' => 150000],
            ['nomor_kamar' => '402', 'kelas' => 'kelas_3', 'tarif' => 150000],
            ['nomor_kamar' => 'ICU-1', 'kelas' => 'icu', 'tarif' => 1200000],
        ];

        foreach ($kamar as $k) {
            Kamar::updateOrCreate(
                ['nomor_kamar' => $k['nomor_kamar']],
                [
                    'unit_kerja_id' => $unitRanap->id,
                    'kelas' => $k['kelas'],
                    'tarif_per_hari' => $k['tarif'],
                ]
            );
        }
    }
}