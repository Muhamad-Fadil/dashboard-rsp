<?php

namespace Database\Seeders;

use App\Models\Pelatihan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PelatihanSeeder extends Seeder
{
    public function run(): void
    {
        $pelatihan = [
            [
                'nama' => 'Pelatihan BHD (Bantuan Hidup Dasar)',
                'penyelenggara' => 'Tim Diklat Internal RSP Goenawan',
                'lokasi' => 'Aula RSP Goenawan Cisarua',
                'mulai' => Carbon::now()->subMonths(3)->format('Y-m-d'),
                'selesai' => Carbon::now()->subMonths(3)->addDays(1)->format('Y-m-d'),
            ],
            [
                'nama' => 'Workshop Patient Safety',
                'penyelenggara' => 'PERSI (Perhimpunan Rumah Sakit Indonesia)',
                'lokasi' => 'Hotel Grand Cisarua',
                'mulai' => Carbon::now()->subMonths(2)->format('Y-m-d'),
                'selesai' => Carbon::now()->subMonths(2)->addDays(2)->format('Y-m-d'),
            ],
            [
                'nama' => 'Pelatihan PPI (Pencegahan & Pengendalian Infeksi)',
                'penyelenggara' => 'Tim PPI RSP Goenawan',
                'lokasi' => 'Ruang Pertemuan Lt. 2',
                'mulai' => Carbon::now()->subMonth()->format('Y-m-d'),
                'selesai' => Carbon::now()->subMonth()->addDays(1)->format('Y-m-d'),
            ],
            [
                'nama' => 'Pelatihan Pelayanan Prima & Komunikasi Efektif',
                'penyelenggara' => 'Tim Diklat Internal RSP Goenawan',
                'lokasi' => 'Aula RSP Goenawan Cisarua',
                'mulai' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'selesai' => Carbon::now()->subDays(9)->format('Y-m-d'),
            ],
        ];

        foreach ($pelatihan as $p) {
            Pelatihan::updateOrCreate(
                ['nama_pelatihan' => $p['nama']],
                [
                    'penyelenggara' => $p['penyelenggara'],
                    'lokasi' => $p['lokasi'],
                    'tanggal_mulai' => $p['mulai'],
                    'tanggal_selesai' => $p['selesai'],
                    'deskripsi' => 'Pelatihan wajib bagi tenaga kesehatan sesuai standar akreditasi rumah sakit.',
                ]
            );
        }
    }
}