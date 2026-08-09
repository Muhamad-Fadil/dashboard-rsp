<?php

namespace Database\Seeders;

use App\Models\Anggaran;
use App\Models\Pengeluaran;
use App\Models\RealisasiAnggaran;
use Illuminate\Database\Seeder;

class RealisasiAnggaranSeeder extends Seeder
{
    public function run(): void
    {
        $semuaPengeluaran = Pengeluaran::all();

        foreach ($semuaPengeluaran as $pengeluaran) {
            $tanggal = $pengeluaran->tanggal;

            $anggaran = Anggaran::where('kategori_pengeluaran_id', $pengeluaran->kategori_pengeluaran_id)
                ->where('tahun', $tanggal->year)
                ->where('bulan', $tanggal->month)
                ->first();

            if (! $anggaran) {
                continue; // skip kalau kebetulan nggak ada anggaran yg cocok
            }

            RealisasiAnggaran::create([
                'anggaran_id' => $anggaran->id,
                'pengeluaran_id' => $pengeluaran->id,
                'jumlah_realisasi' => $pengeluaran->jumlah,
                'tanggal' => $tanggal,
            ]);
        }
    }
}