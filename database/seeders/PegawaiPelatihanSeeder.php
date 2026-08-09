<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\Pelatihan;
use App\Models\PegawaiPelatihan;
use Illuminate\Database\Seeder;

class PegawaiPelatihanSeeder extends Seeder
{
    public function run(): void
    {
        $pegawaiList = Pegawai::all();
        $pelatihanList = Pelatihan::all();

        foreach ($pelatihanList as $pelatihan) {
            // tiap pelatihan diikuti 8-15 pegawai acak
            $peserta = $pegawaiList->random(fake()->numberBetween(8, 15));

            foreach ($peserta as $pegawai) {
                $sudahLewat = $pelatihan->tanggal_selesai->isPast();

                PegawaiPelatihan::updateOrCreate(
                    ['pegawai_id' => $pegawai->id, 'pelatihan_id' => $pelatihan->id],
                    [
                        'status' => $sudahLewat ? fake()->randomElement(['selesai', 'selesai', 'selesai', 'tidak_hadir']) : 'terdaftar',
                        'sertifikat' => $sudahLewat ? 'sertifikat-' . $pegawai->nip . '.pdf' : null,
                    ]
                );
            }
        }
    }
}