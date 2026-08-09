<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Piutang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PiutangSeeder extends Seeder
{
    public function run(): void
    {
        // ambil ~15% dari kunjungan yang sudah selesai, dianggap pasien umum yang belum lunas
        $kunjunganSelesai = Kunjungan::where('status', 'selesai')
            ->inRandomOrder()
            ->limit((int) (Kunjungan::where('status', 'selesai')->count() * 0.15))
            ->get();

        foreach ($kunjunganSelesai as $k) {
            $tagihan = fake()->numberBetween(300_000, 5_000_000);

            // status acak: sebagian belum bayar sama sekali, sebagian bayar sebagian, sebagian udah lunas
            $status = fake()->randomElement(['belum_lunas', 'belum_lunas', 'sebagian', 'lunas']);

            $terbayar = match ($status) {
                'belum_lunas' => 0,
                'sebagian' => round($tagihan * fake()->randomFloat(2, 0.2, 0.7)),
                'lunas' => $tagihan,
            };

            $tanggalTagihan = $k->waktu_selesai ?? $k->waktu_daftar;

            Piutang::create([
                'pasien_id' => $k->pasien_id,
                'kunjungan_id' => $k->id,
                'jumlah_tagihan' => $tagihan,
                'jumlah_terbayar' => $terbayar,
                'tanggal_tagihan' => $tanggalTagihan,
                'jatuh_tempo' => Carbon::parse($tanggalTagihan)->addDays(30),
                'status' => $status,
                'keterangan' => $status === 'lunas' ? 'Sudah dilunasi' : 'Tagihan pasien umum',
            ]);
        }
    }
}