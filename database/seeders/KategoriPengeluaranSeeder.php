<?php

namespace Database\Seeders;

use App\Models\KategoriPengeluaran;
use Illuminate\Database\Seeder;

class KategoriPengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['kode' => 'PGL-01', 'nama' => 'Belanja Pegawai'],
            ['kode' => 'PGL-02', 'nama' => 'Belanja Obat & Alkes'],
            ['kode' => 'PGL-03', 'nama' => 'Belanja Operasional'],
            ['kode' => 'PGL-04', 'nama' => 'Belanja Pemeliharaan'],
            ['kode' => 'PGL-05', 'nama' => 'Belanja Modal'],
            ['kode' => 'PGL-06', 'nama' => 'Belanja Listrik & Air'],
        ];

        foreach ($kategori as $k) {
            KategoriPengeluaran::updateOrCreate(
                ['kode' => $k['kode']],
                ['nama_kategori' => $k['nama']]
            );
        }
    }
}