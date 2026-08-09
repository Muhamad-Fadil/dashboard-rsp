<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Resep;
use Illuminate\Database\Seeder;

class ResepSeeder extends Seeder
{
    public function run(): void
    {
        // resep cuma dibuat dari kunjungan yang statusnya sudah selesai
        $kunjunganSelesai = Kunjungan::where('status', 'selesai')
            ->whereNotNull('dokter_id')
            ->inRandomOrder()
            ->limit((int) (Kunjungan::where('status', 'selesai')->count() * 0.6)) // ~60% kunjungan selesai dikasih resep
            ->get();

        $nomorUrut = 1;

        foreach ($kunjunganSelesai as $k) {
            $tanggalResep = $k->waktu_selesai ?? $k->waktu_daftar->copy()->addMinutes(30);

            Resep::create([
                'no_resep' => 'RSP-' . $tanggalResep->format('Ymd') . '-' . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT),
                'kunjungan_id' => $k->id,
                'dokter_id' => $k->dokter_id,
                'tanggal_resep' => $tanggalResep,
                'status' => 'selesai',
            ]);

            $nomorUrut++;
        }
    }
}