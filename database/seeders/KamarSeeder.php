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
            // Ruang Mawar (Mawar Bawah) - umum
            ['nomor_kamar' => 'MW-B1', 'bangsal' => 'Mawar Bawah', 'kelas' => 'kelas_3', 'tarif' => 150000],
            ['nomor_kamar' => 'MW-B2', 'bangsal' => 'Mawar Bawah', 'kelas' => 'kelas_3', 'tarif' => 150000],
            ['nomor_kamar' => 'MW-B3', 'bangsal' => 'Mawar Bawah', 'kelas' => 'kelas_3', 'tarif' => 150000],

            // Ruang Melati - VIP, Kelas 1, dan HCU
            ['nomor_kamar' => 'ML-V1', 'bangsal' => 'Melati', 'kelas' => 'vip', 'tarif' => 750000],
            ['nomor_kamar' => 'ML-K1', 'bangsal' => 'Melati', 'kelas' => 'kelas_1', 'tarif' => 450000],
            ['nomor_kamar' => 'ML-K2', 'bangsal' => 'Melati', 'kelas' => 'kelas_1', 'tarif' => 450000],
            ['nomor_kamar' => 'ML-H1', 'bangsal' => 'Melati', 'kelas' => 'hcu', 'tarif' => 950000],

            // Ruang Kacapiring - pasien paru non-infeksius & umum
            ['nomor_kamar' => 'KP-1', 'bangsal' => 'Kacapiring', 'kelas' => 'kelas_2', 'tarif' => 300000],
            ['nomor_kamar' => 'KP-2', 'bangsal' => 'Kacapiring', 'kelas' => 'kelas_2', 'tarif' => 300000],

            // Ruang Anggrek - Anggrek Atas & Anggrek Bawah
            ['nomor_kamar' => 'AG-A1', 'bangsal' => 'Anggrek Atas', 'kelas' => 'kelas_1', 'tarif' => 450000],
            ['nomor_kamar' => 'AG-A2', 'bangsal' => 'Anggrek Atas', 'kelas' => 'kelas_1', 'tarif' => 450000],
            ['nomor_kamar' => 'AG-B1', 'bangsal' => 'Anggrek Bawah', 'kelas' => 'kelas_2', 'tarif' => 300000],
            ['nomor_kamar' => 'AG-B2', 'bangsal' => 'Anggrek Bawah', 'kelas' => 'kelas_2', 'tarif' => 300000],

            // Ruang Tanjung
            ['nomor_kamar' => 'TJ-1', 'bangsal' => 'Tanjung', 'kelas' => 'kelas_2', 'tarif' => 300000],
            ['nomor_kamar' => 'TJ-2', 'bangsal' => 'Tanjung', 'kelas' => 'kelas_2', 'tarif' => 300000],

            // Ruang Teratai
            ['nomor_kamar' => 'TR-1', 'bangsal' => 'Teratai', 'kelas' => 'kelas_3', 'tarif' => 150000],
            ['nomor_kamar' => 'TR-2', 'bangsal' => 'Teratai', 'kelas' => 'kelas_3', 'tarif' => 150000],

            // Ruang ICU & ICU Isolasi
            ['nomor_kamar' => 'ICU-1', 'bangsal' => 'ICU', 'kelas' => 'icu', 'tarif' => 1200000],
            ['nomor_kamar' => 'ICU-2', 'bangsal' => 'ICU', 'kelas' => 'icu', 'tarif' => 1200000],
            ['nomor_kamar' => 'ICU-ISO-1', 'bangsal' => 'ICU Isolasi', 'kelas' => 'icu', 'tarif' => 1500000],
        ];

        foreach ($kamar as $k) {
            Kamar::updateOrCreate(
                ['nomor_kamar' => $k['nomor_kamar']],
                [
                    'nama_bangsal' => $k['bangsal'],
                    'unit_kerja_id' => $unitRanap->id,
                    'kelas' => $k['kelas'],
                    'tarif_per_hari' => $k['tarif'],
                ]
            );
        }
    }
}