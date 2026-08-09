<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Radiologi;
use App\Models\User;
use Illuminate\Database\Seeder;

class RadiologiSeeder extends Seeder
{
    public function run(): void
    {
        $petugasId = User::where('email', 'operator.layanan@rspgoenawan.co.id')->first()?->id;

        // ambil ~15% dari semua kunjungan buat dijadwalkan radiologi
        $kunjungan = Kunjungan::inRandomOrder()->limit((int) (Kunjungan::count() * 0.15))->get();

        $jenisPemeriksaan = [
            'Rontgen Thorax', 'CT Scan Kepala', 'USG Abdomen', 'Rontgen Ekstremitas', 'USG Kandungan',
        ];

        foreach ($kunjungan as $k) {
            $waktuPeriksa = $k->waktu_daftar->copy()->addMinutes(fake()->numberBetween(20, 90));
            $sudahSelesai = $k->status === 'selesai';

            Radiologi::create([
                'kunjungan_id' => $k->id,
                'user_id' => $petugasId,
                'jenis_pemeriksaan' => fake()->randomElement($jenisPemeriksaan),
                'waktu_periksa' => $waktuPeriksa,
                'hasil' => $sudahSelesai ? 'Tidak tampak kelainan bermakna' : null,
                'status' => $sudahSelesai ? 'selesai' : 'menunggu',
            ]);
        }
    }
}