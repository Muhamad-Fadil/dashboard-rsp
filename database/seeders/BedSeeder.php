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
            // ICU & HCU cuma 1 bed per "kamar" (perawatan intensif/high care), kamar biasa 2 bed
            $jumlahBed = in_array($kamar->kelas, ['icu', 'hcu']) ? 1 : 2;

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