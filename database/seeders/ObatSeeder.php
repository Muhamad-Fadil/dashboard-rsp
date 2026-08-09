<?php

namespace Database\Seeders;

use App\Models\Obat;
use Illuminate\Database\Seeder;

class ObatSeeder extends Seeder
{
    public function run(): void
    {
        $obat = [
            ['kode' => 'OBT-001', 'nama' => 'Paracetamol 500mg', 'satuan' => 'tablet', 'harga' => 500, 'stok' => 5000],
            ['kode' => 'OBT-002', 'nama' => 'Amoxicillin 500mg', 'satuan' => 'tablet', 'harga' => 1200, 'stok' => 3000],
            ['kode' => 'OBT-003', 'nama' => 'Antasida Sirup', 'satuan' => 'botol', 'harga' => 15000, 'stok' => 500],
            ['kode' => 'OBT-004', 'nama' => 'Vitamin C 500mg', 'satuan' => 'tablet', 'harga' => 800, 'stok' => 4000],
            ['kode' => 'OBT-005', 'nama' => 'Omeprazole 20mg', 'satuan' => 'kapsul', 'harga' => 2500, 'stok' => 2000],
            ['kode' => 'OBT-006', 'nama' => 'Ibuprofen 400mg', 'satuan' => 'tablet', 'harga' => 900, 'stok' => 3500],
            ['kode' => 'OBT-007', 'nama' => 'Cetirizine 10mg', 'satuan' => 'tablet', 'harga' => 700, 'stok' => 2500],
            ['kode' => 'OBT-008', 'nama' => 'Infus RL 500ml', 'satuan' => 'botol', 'harga' => 25000, 'stok' => 800],
            ['kode' => 'OBT-009', 'nama' => 'Salbutamol Inhaler', 'satuan' => 'buah', 'harga' => 45000, 'stok' => 200],
            ['kode' => 'OBT-010', 'nama' => 'Metformin 500mg', 'satuan' => 'tablet', 'harga' => 1000, 'stok' => 3000],
        ];

        foreach ($obat as $o) {
            Obat::updateOrCreate(
                ['kode_obat' => $o['kode']],
                [
                    'nama_obat' => $o['nama'],
                    'satuan' => $o['satuan'],
                    'harga_satuan' => $o['harga'],
                    'stok' => $o['stok'],
                    'aktif' => true,
                ]
            );
        }
    }
}