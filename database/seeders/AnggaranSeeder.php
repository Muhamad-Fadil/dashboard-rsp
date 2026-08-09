<?php

namespace Database\Seeders;

use App\Models\Anggaran;
use App\Models\KategoriPengeluaran;
use App\Models\Pengeluaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AnggaranSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriList = KategoriPengeluaran::all();

        for ($bulanKe = 5; $bulanKe >= 0; $bulanKe--) {
            $bulanAcuan = Carbon::now()->subMonths($bulanKe);

            foreach ($kategoriList as $kategori) {
                // total pengeluaran aktual bulan ini utk kategori ini, dipakai sbg acuan
                $totalAktual = Pengeluaran::where('kategori_pengeluaran_id', $kategori->id)
                    ->whereYear('tanggal', $bulanAcuan->year)
                    ->whereMonth('tanggal', $bulanAcuan->month)
                    ->sum('jumlah');

                // anggaran dibuat sedikit lebih besar dari aktual (biar realisasi ~75-95%)
                $anggaran = $totalAktual > 0
                    ? round($totalAktual / fake()->randomFloat(2, 0.75, 0.95))
                    : fake()->numberBetween(5_000_000, 20_000_000); // fallback kalau kebetulan gaada transaksi

                Anggaran::updateOrCreate(
                    [
                        'kategori_pengeluaran_id' => $kategori->id,
                        'unit_kerja_id' => null,
                        'tahun' => $bulanAcuan->year,
                        'bulan' => $bulanAcuan->month,
                    ],
                    [
                        'jumlah_anggaran' => $anggaran,
                        'keterangan' => 'Anggaran ' . $kategori->nama_kategori . ' periode ' . $bulanAcuan->format('F Y'),
                    ]
                );
            }
        }
    }
}