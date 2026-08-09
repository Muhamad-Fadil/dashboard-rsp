<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Profesi;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $perawat = Profesi::where('nama_profesi', 'Perawat')->first();
        $bidan = Profesi::where('nama_profesi', 'Bidan')->first();
        $apoteker = Profesi::where('nama_profesi', 'Apoteker')->first();
        $analis = Profesi::where('nama_profesi', 'Analis Laboratorium')->first();
        $radiografer = Profesi::where('nama_profesi', 'Radiografer')->first();
        $admin = Profesi::where('nama_profesi', 'Staf Administrasi')->first();
        $keamanan = Profesi::where('nama_profesi', 'Petugas Keamanan')->first();
        $cleaning = Profesi::where('nama_profesi', 'Cleaning Service')->first();

        $unitIgd = UnitKerja::where('kode_unit', 'IGD')->first();
        $unitRanap = UnitKerja::where('kode_unit', 'RANAP')->first();
        $unitIbs = UnitKerja::where('kode_unit', 'IBS')->first();
        $unitFarmasi = UnitKerja::where('kode_unit', 'FARM')->first();
        $unitLab = UnitKerja::where('kode_unit', 'LAB')->first();
        $unitRad = UnitKerja::where('kode_unit', 'RAD')->first();
        $unitPoli = UnitKerja::where('kode_unit', 'POLI')->first();
        $unitSdm1 = UnitKerja::where('kode_unit', 'SDM-01')->first();
        $unitSdm2 = UnitKerja::where('kode_unit', 'SDM-02')->first();
        $unitKeu1 = UnitKerja::where('kode_unit', 'KEU-01')->first();
        $unitKeu2 = UnitKerja::where('kode_unit', 'KEU-02')->first();
        $unitKeu3 = UnitKerja::where('kode_unit', 'KEU-03')->first();

        $pegawai = [
            // Perawat - IGD
            ['nama' => 'Rina Marlina', 'profesi' => $perawat, 'unit' => $unitIgd, 'jk' => 'P', 'masuk' => '2019-03-01'],
            ['nama' => 'Agus Setiawan', 'profesi' => $perawat, 'unit' => $unitIgd, 'jk' => 'L', 'masuk' => '2020-06-15'],
            ['nama' => 'Yuni Kartika', 'profesi' => $perawat, 'unit' => $unitIgd, 'jk' => 'P', 'masuk' => '2021-01-10'],

            // Perawat - Rawat Inap
            ['nama' => 'Dedi Kurniawan', 'profesi' => $perawat, 'unit' => $unitRanap, 'jk' => 'L', 'masuk' => '2018-09-01'],
            ['nama' => 'Sri Wahyuni', 'profesi' => $perawat, 'unit' => $unitRanap, 'jk' => 'P', 'masuk' => '2019-11-20'],
            ['nama' => 'Tono Wijaya', 'profesi' => $perawat, 'unit' => $unitRanap, 'jk' => 'L', 'masuk' => '2020-02-14'],
            ['nama' => 'Lina Marlina', 'profesi' => $perawat, 'unit' => $unitRanap, 'jk' => 'P', 'masuk' => '2022-05-05'],
            ['nama' => 'Eko Prasetyo', 'profesi' => $perawat, 'unit' => $unitRanap, 'jk' => 'L', 'masuk' => '2021-08-17'],

            // Perawat - Poli & IBS
            ['nama' => 'Diah Permata', 'profesi' => $perawat, 'unit' => $unitPoli, 'jk' => 'P', 'masuk' => '2020-04-01'],
            ['nama' => 'Bayu Aditya', 'profesi' => $perawat, 'unit' => $unitIbs, 'jk' => 'L', 'masuk' => '2019-07-22'],
            ['nama' => 'Farah Nabila', 'profesi' => $perawat, 'unit' => $unitIbs, 'jk' => 'P', 'masuk' => '2021-03-30'],

            // Bidan
            ['nama' => 'Wulan Sari', 'profesi' => $bidan, 'unit' => $unitPoli, 'jk' => 'P', 'masuk' => '2018-05-12'],
            ['nama' => 'Indah Puspita', 'profesi' => $bidan, 'unit' => $unitRanap, 'jk' => 'P', 'masuk' => '2020-10-01'],

            // Apoteker & Farmasi
            ['nama' => 'Rizky Ramadhan', 'profesi' => $apoteker, 'unit' => $unitFarmasi, 'jk' => 'L', 'masuk' => '2019-01-15'],
            ['nama' => 'Melati Putri', 'profesi' => $apoteker, 'unit' => $unitFarmasi, 'jk' => 'P', 'masuk' => '2021-06-01'],

            // Analis Lab
            ['nama' => 'Hendra Gunawan', 'profesi' => $analis, 'unit' => $unitLab, 'jk' => 'L', 'masuk' => '2018-12-01'],
            ['nama' => 'Citra Dewi', 'profesi' => $analis, 'unit' => $unitLab, 'jk' => 'P', 'masuk' => '2020-08-20'],

            // Radiografer
            ['nama' => 'Fajar Ramadhan', 'profesi' => $radiografer, 'unit' => $unitRad, 'jk' => 'L', 'masuk' => '2019-09-10'],
            ['nama' => 'Nadia Salsabila', 'profesi' => $radiografer, 'unit' => $unitRad, 'jk' => 'P', 'masuk' => '2022-02-01'],

            // Staf Administrasi (SDM & Keuangan)
            ['nama' => 'Anisa Putri', 'profesi' => $admin, 'unit' => $unitSdm1, 'jk' => 'P', 'masuk' => '2017-04-01'],
            ['nama' => 'Bagus Prakoso', 'profesi' => $admin, 'unit' => $unitSdm2, 'jk' => 'L', 'masuk' => '2019-05-20'],
            ['nama' => 'Dian Anggraini', 'profesi' => $admin, 'unit' => $unitKeu1, 'jk' => 'P', 'masuk' => '2018-02-10'],
            ['nama' => 'Fitriani', 'profesi' => $admin, 'unit' => $unitKeu1, 'jk' => 'P', 'masuk' => '2020-11-01'],
            ['nama' => 'Galih Pratama', 'profesi' => $admin, 'unit' => $unitKeu2, 'jk' => 'L', 'masuk' => '2019-10-01'],
            ['nama' => 'Hesti Rahayu', 'profesi' => $admin, 'unit' => $unitKeu3, 'jk' => 'P', 'masuk' => '2021-07-15'],

            // Petugas Keamanan & Cleaning
            ['nama' => 'Joko Susilo', 'profesi' => $keamanan, 'unit' => $unitIgd, 'jk' => 'L', 'masuk' => '2018-01-01'],
            ['nama' => 'Wahyu Hidayat', 'profesi' => $keamanan, 'unit' => $unitRanap, 'jk' => 'L', 'masuk' => '2020-03-01'],
            ['nama' => 'Sumiati', 'profesi' => $cleaning, 'unit' => $unitRanap, 'jk' => 'P', 'masuk' => '2019-06-01'],
        ];

        foreach ($pegawai as $i => $p) {
            $nip = 'PEG-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            Pegawai::updateOrCreate(
                ['nip' => $nip],
                [
                    'nama' => $p['nama'],
                    'profesi_id' => $p['profesi']->id,
                    'unit_kerja_id' => $p['unit']->id,
                    'jenis_kelamin' => $p['jk'],
                    'tanggal_lahir' => null,
                    'tanggal_masuk' => $p['masuk'],
                    'status_kepegawaian' => 'tetap',
                    'no_hp' => '0813' . rand(10000000, 99999999),
                    'aktif' => true,
                ]
            );
        }
    }
}