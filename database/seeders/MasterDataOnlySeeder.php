<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MasterDataOnlySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DivisionSeeder::class,
            UserSeeder::class,
            UnitKerjaSeeder::class,
            PoliSeeder::class,
            ProfesiSeeder::class,
            DokterSeeder::class,
            PegawaiSeeder::class,
            KamarSeeder::class, 
            BedSeeder::class,
            ObatSeeder::class,
            ReferensiSeeder::class,
            ShiftSeeder::class,
            KategoriPendapatanSeeder::class,
            KategoriPengeluaranSeeder::class,
        ]);
    }
}