<?php

namespace Database\Seeders;

use App\Models\KategoriPendapatan;
use App\Models\Pendapatan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PendapatanSeeder extends Seeder
{
    public function run(): void
    {
        $operatorId = User::where('email', 'operator.keuangan@rspgoenawan.co.id')->first()?->id;
        $kategoriList = KategoriPendapatan::all();
        $unitLayananIds = UnitKerja::whereHas('division', fn ($q) => $q->where('slug', 'layanan'))->pluck('id')->all();

        // rentang nominal per kategori (per transaksi), disesuaikan skala rumah sakit kecil-menengah
        $rentangNominal = [
            'PDT-01' => [800_000, 3_000_000],   // Rawat Jalan
            'PDT-02' => [2_000_000, 8_000_000], // Rawat Inap
            'PDT-03' => [200_000, 1_000_000],   // Laboratorium
            'PDT-04' => [300_000, 1_500_000],   // Radiologi
            'PDT-05' => [100_000, 800_000],     // Farmasi
            'PDT-06' => [3_000_000, 15_000_000], // Operasi
            'PDT-07' => [1_500_000, 6_000_000], // Klaim BPJS
        ];

        for ($bulanKe = 5; $bulanKe >= 0; $bulanKe--) {
            $bulanAcuan = Carbon::now()->subMonths($bulanKe);
            // faktor pertumbuhan kecil tiap bulan mendekati sekarang, biar grafik trennya naik
            $faktorTumbuh = 1 + ((5 - $bulanKe) * 0.06);

            foreach ($kategoriList as $kategori) {
                [$min, $max] = $rentangNominal[$kategori->kode] ?? [500_000, 2_000_000];

                // 4-6 transaksi per kategori per bulan
                $jumlahTransaksi = fake()->numberBetween(4, 6);

                for ($i = 0; $i < $jumlahTransaksi; $i++) {
                    $tanggal = fake()->dateTimeBetween(
                        $bulanAcuan->copy()->startOfMonth(),
                        $bulanAcuan->copy()->endOfMonth()->min(Carbon::now())
                    );

                    $nominal = fake()->numberBetween($min, $max) * $faktorTumbuh;

                    Pendapatan::create([
                        'kategori_pendapatan_id' => $kategori->id,
                        'unit_kerja_id' => fake()->randomElement($unitLayananIds),
                        'kunjungan_id' => null,
                        'tanggal' => $tanggal,
                        'jumlah' => round($nominal, 2),
                        'keterangan' => 'Pendapatan ' . $kategori->nama_kategori . ' periode ' . $bulanAcuan->format('F Y'),
                        'user_id' => $operatorId,
                    ]);
                }
            }
        }
    }
}