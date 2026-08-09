<?php

namespace Database\Seeders;

use App\Models\Referensi;
use Illuminate\Database\Seeder;

class ReferensiSeeder extends Seeder
{
    public function run(): void
    {
        $referensi = [
            // jenis_pembayaran
            ['kategori' => 'jenis_pembayaran', 'kode' => 'umum', 'nilai' => 'Umum', 'urutan' => 1],
            ['kategori' => 'jenis_pembayaran', 'kode' => 'bpjs', 'nilai' => 'BPJS', 'urutan' => 2],
            ['kategori' => 'jenis_pembayaran', 'kode' => 'asuransi', 'nilai' => 'Asuransi Swasta', 'urutan' => 3],

            // golongan_darah
            ['kategori' => 'golongan_darah', 'kode' => 'a', 'nilai' => 'A', 'urutan' => 1],
            ['kategori' => 'golongan_darah', 'kode' => 'b', 'nilai' => 'B', 'urutan' => 2],
            ['kategori' => 'golongan_darah', 'kode' => 'ab', 'nilai' => 'AB', 'urutan' => 3],
            ['kategori' => 'golongan_darah', 'kode' => 'o', 'nilai' => 'O', 'urutan' => 4],

            // agama
            ['kategori' => 'agama', 'kode' => 'islam', 'nilai' => 'Islam', 'urutan' => 1],
            ['kategori' => 'agama', 'kode' => 'kristen', 'nilai' => 'Kristen', 'urutan' => 2],
            ['kategori' => 'agama', 'kode' => 'katolik', 'nilai' => 'Katolik', 'urutan' => 3],
            ['kategori' => 'agama', 'kode' => 'hindu', 'nilai' => 'Hindu', 'urutan' => 4],
            ['kategori' => 'agama', 'kode' => 'buddha', 'nilai' => 'Buddha', 'urutan' => 5],
        ];

        foreach ($referensi as $r) {
            Referensi::updateOrCreate(
                ['kategori' => $r['kategori'], 'kode' => $r['kode']],
                $r
            );
        }
    }
}