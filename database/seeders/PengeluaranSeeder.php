<?php

namespace Database\Seeders;

use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        $operatorId = User::where('email', 'operator.keuangan@rspgoenawan.co.id')->first()?->id;
        $kategoriList = KategoriPengeluaran::all();
        $unitIds = UnitKerja::pluck('id')->all();

        $rentangNominal = [
            'PGL-01' => [15_000_000, 40_000_000], // Belanja Pegawai (gaji, biasanya paling besar)
            'PGL-02' => [3_000_000, 12_000_000],  // Obat & Alkes
            'PGL-03' => [1_000_000, 5_000_000],   // Operasional
            'PGL-04' => [500_000, 3_000_000],     // Pemeliharaan
            'PGL-05' => [2_000_000, 10_000_000],  // Belanja Modal
            'PGL-06' => [1_500_000, 4_000_000],   // Listrik & Air
        ];

        for ($bulanKe = 5; $bulanKe >= 0; $bulanKe--) {
            $bulanAcuan = Carbon::now()->subMonths($bulanKe);
            $faktorTumbuh = 1 + ((5 - $bulanKe) * 0.04); // naik lebih pelan dari pendapatan

            foreach ($kategoriList as $kategori) {
                [$min, $max] = $rentangNominal[$kategori->kode] ?? [1_000_000, 3_000_000];

                // belanja pegawai cukup 1x per bulan (gaji), yang lain 2-4x
                $jumlahTransaksi = $kategori->kode === 'PGL-01' ? 1 : fake()->numberBetween(2, 4);

                for ($i = 0; $i < $jumlahTransaksi; $i++) {
                    $tanggal = fake()->dateTimeBetween(
                        $bulanAcuan->copy()->startOfMonth(),
                        $bulanAcuan->copy()->endOfMonth()->min(Carbon::now())
                    );

                    $nominal = fake()->numberBetween($min, $max) * $faktorTumbuh;

                    Pengeluaran::create([
                        'kategori_pengeluaran_id' => $kategori->id,
                        'unit_kerja_id' => fake()->randomElement($unitIds),
                        'tanggal' => $tanggal,
                        'jumlah' => round($nominal, 2),
                        'keterangan' => 'Pengeluaran ' . $kategori->nama_kategori . ' periode ' . $bulanAcuan->format('F Y'),
                        'user_id' => $operatorId,
                    ]);
                }
            }
        }
    }
}