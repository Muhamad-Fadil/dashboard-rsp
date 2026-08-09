<?php

namespace Database\Seeders;

use App\Models\Obat;
use App\Models\Resep;
use App\Models\ResepDetail;
use Illuminate\Database\Seeder;

class ResepDetailSeeder extends Seeder
{
    public function run(): void
    {
        $obatList = Obat::all();
        $aturanPakai = ['3x1 sesudah makan', '2x1 sebelum makan', '1x1 malam hari', '3x1 sesudah makan', 'Bila perlu'];

        $resepList = Resep::all();

        foreach ($resepList as $resep) {
            $jumlahJenisObat = fake()->numberBetween(1, 3);
            $obatDipilih = $obatList->random(min($jumlahJenisObat, $obatList->count()));

            foreach ($obatDipilih as $obat) {
                $jumlah = fake()->numberBetween(6, 20);

                ResepDetail::create([
                    'resep_id' => $resep->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $jumlah,
                    'aturan_pakai' => fake()->randomElement($aturanPakai),
                    'harga_saat_itu' => $obat->harga_satuan,
                ]);
            }
        }
    }
}