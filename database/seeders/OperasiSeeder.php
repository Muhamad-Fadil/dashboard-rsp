<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Operasi;
use App\Models\Profesi;
use App\Models\RawatInap;
use Illuminate\Database\Seeder;

class OperasiSeeder extends Seeder
{
    public function run(): void
    {
        // ambil dokter yang profesinya spesialis bedah
        $profesiBedah = Profesi::where('nama_profesi', 'Dokter Spesialis Bedah')->first();
        $dokterBedahIds = Dokter::where('profesi_id', $profesiBedah?->id)->pluck('id')->all();

        if (empty($dokterBedahIds)) {
            $dokterBedahIds = Dokter::pluck('id')->all(); // fallback kalau nggak ketemu
        }

        // ambil ~40% dari rawat inap buat dijadwalkan operasi
        $rawatInap = RawatInap::inRandomOrder()->limit((int) (RawatInap::count() * 0.4))->get();

        $jenisOperasi = [
            'Apendiktomi', 'Seksio Sesarea', 'Herniotomi', 'Kolesistektomi',
            'Debridement Luka', 'Kuretase', 'Tonsilektomi',
        ];

        foreach ($rawatInap as $ri) {
            $mulai = $ri->tanggal_masuk->copy()->addHours(fake()->numberBetween(2, 24));
            $sudahSelesai = $mulai->lt(now()->subHours(3));

            Operasi::create([
                'kunjungan_id' => $ri->kunjungan_id,
                'dokter_id' => fake()->randomElement($dokterBedahIds),
                'jenis_operasi' => fake()->randomElement($jenisOperasi),
                'ruang_operasi' => 'OK ' . fake()->numberBetween(1, 3),
                'waktu_mulai' => $mulai,
                'waktu_selesai' => $sudahSelesai ? $mulai->copy()->addMinutes(fake()->numberBetween(45, 180)) : null,
                'status' => $sudahSelesai ? 'selesai' : 'dijadwalkan',
                'catatan' => 'Tindakan berjalan sesuai prosedur standar',
            ]);
        }
    }
}