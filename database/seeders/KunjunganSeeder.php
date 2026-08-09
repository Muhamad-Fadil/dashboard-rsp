<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KunjunganSeeder extends Seeder
{
    public function run(): void
    {
        $pasienIds = Pasien::pluck('id')->all();
        $poliIds = Poli::pluck('id')->all();
        $dokterIds = Dokter::pluck('id')->all();
        $operatorId = User::where('email', 'operator.layanan@rspgoenawan.co.id')->first()?->id;

        // jumlah kunjungan per bulan, makin baru makin banyak (biar grafik trennya naik)
        $targetPerBulan = [
            5 => 25,   // Maret
            4 => 30,   // April
            3 => 35,   // Mei
            2 => 40,   // Juni
            1 => 45,   // Juli
            0 => 20,   // Agustus (baru jalan setengah bulan)
        ];

        $nomorUrut = 1;

        foreach ($targetPerBulan as $bulanKe => $jumlah) {
            $bulanAcuan = Carbon::now()->subMonths($bulanKe);

            for ($i = 0; $i < $jumlah; $i++) {
                $tanggal = fake()->dateTimeBetween(
                    $bulanAcuan->copy()->startOfMonth(),
                    $bulanAcuan->copy()->endOfMonth()->min(Carbon::now())
                );

                $waktuDaftar = Carbon::instance($tanggal)->setTime(
                    fake()->numberBetween(7, 15),
                    fake()->numberBetween(0, 59)
                );

                // waktu tunggu acak 10-90 menit, sebagian kecil "menunggu" kalau ini hari ini
                $sudahSelesai = $waktuDaftar->lt(Carbon::now()->subHours(2));

                $waktuDilayani = $sudahSelesai
                    ? $waktuDaftar->copy()->addMinutes(fake()->numberBetween(10, 90))
                    : null;

                $waktuSelesai = $sudahSelesai
                    ? $waktuDilayani->copy()->addMinutes(fake()->numberBetween(15, 45))
                    : null;

                Kunjungan::create([
                    'no_kunjungan' => 'KJ-' . $waktuDaftar->format('Ymd') . '-' . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT),
                    'pasien_id' => fake()->randomElement($pasienIds),
                    'poli_id' => fake()->randomElement($poliIds),
                    'dokter_id' => fake()->randomElement($dokterIds),
                    'jenis_kunjungan' => fake()->randomElement(['rawat_jalan', 'rawat_jalan', 'rawat_jalan', 'rawat_inap', 'igd']),
                    'keluhan' => fake('id_ID')->randomElement([
                        'Demam dan batuk', 'Sakit kepala', 'Nyeri perut', 'Pusing dan mual',
                        'Kontrol rutin', 'Nyeri sendi', 'Sesak napas ringan', 'Luka ringan',
                    ]),
                    'status' => $sudahSelesai ? 'selesai' : 'menunggu',
                    'waktu_daftar' => $waktuDaftar,
                    'waktu_dilayani' => $waktuDilayani,
                    'waktu_selesai' => $waktuSelesai,
                    'user_id' => $operatorId,
                ]);

                $nomorUrut++;
            }
        }
    }
}