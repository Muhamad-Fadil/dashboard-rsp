<?php

namespace Database\Seeders;

use App\Models\KategoriPendapatan;
use Illuminate\Database\Seeder;

class KategoriPendapatanSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['kode' => 'PDT-01', 'nama' => 'Pendapatan Rawat Jalan'],
            ['kode' => 'PDT-02', 'nama' => 'Pendapatan Rawat Inap'],
            ['kode' => 'PDT-03', 'nama' => 'Pendapatan Laboratorium'],
            ['kode' => 'PDT-04', 'nama' => 'Pendapatan Radiologi'],
            ['kode' => 'PDT-05', 'nama' => 'Pendapatan Farmasi'],
            ['kode' => 'PDT-06', 'nama' => 'Pendapatan Operasi'],
            ['kode' => 'PDT-07', 'nama' => 'Pendapatan Klaim BPJS'],
        ];

        foreach ($kategori as $k) {
            KategoriPendapatan::updateOrCreate(
                ['kode' => $k['kode']],
                ['nama_kategori' => $k['nama']]
            );
        }
    }
}