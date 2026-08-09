<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Kamar;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    public function run(): void
    {
        $semuaKamar = Kamar::all();

        foreach ($semuaKamar as $kamar) {
            // ICU cuma 1 bed per "kamar", kamar biasa 2 bed
            $jumlahBed = $kamar->kelas === 'icu' ? 1 : 2;

            for ($i = 1; $i <= $jumlahBed; $i++) {
                $nomorBed = $jumlahBed === 1 ? 'A' : chr(64 + $i); // A, B

                Bed::updateOrCreate(
                    ['kamar_id' => $kamar->id, 'nomor_bed' => $nomorBed],
                    ['status' => 'tersedia']
                );
            }
        }
    }
}