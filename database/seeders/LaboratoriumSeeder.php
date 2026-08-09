<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Laboratorium;
use App\Models\User;
use Illuminate\Database\Seeder;

class LaboratoriumSeeder extends Seeder
{
    public function run(): void
    {
        $petugasId = User::where('email', 'operator.layanan@rspgoenawan.co.id')->first()?->id;

        // ambil ~35% dari semua kunjungan buat dijadwalkan lab
        $kunjungan = Kunjungan::inRandomOrder()->limit((int) (Kunjungan::count() * 0.35))->get();

        $jenisPemeriksaan = [
            'Darah Lengkap', 'Gula Darah Sewaktu', 'Kolesterol', 'Fungsi Ginjal',
            'Fungsi Hati', 'Urine Lengkap', 'Asam Urat', 'HbA1c',
        ];

        foreach ($kunjungan as $k) {
            $waktuPeriksa = $k->waktu_daftar->copy()->addMinutes(fake()->numberBetween(15, 60));
            $sudahSelesai = $k->status === 'selesai';

            Laboratorium::create([
                'kunjungan_id' => $k->id,
                'user_id' => $petugasId,
                'jenis_pemeriksaan' => fake()->randomElement($jenisPemeriksaan),
                'waktu_periksa' => $waktuPeriksa,
                'hasil' => $sudahSelesai ? 'Hasil dalam batas normal' : null,
                'status' => $sudahSelesai ? 'selesai' : 'menunggu',
            ]);
        }
    }
}