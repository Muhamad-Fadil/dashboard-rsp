<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\PenilaianKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PenilaianKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $manajerLayanan = User::where('email', 'manajer.layanan@rspgoenawan.co.id')->first();
        $manajerSdm = User::where('email', 'manajer.sdm@rspgoenawan.co.id')->first();
        $manajerKeuangan = User::where('email', 'manajer.keuangan@rspgoenawan.co.id')->first();

        $pegawaiList = Pegawai::where('aktif', true)->get();

        // penilaian buat 3 bulan terakhir
        for ($bulanKe = 2; $bulanKe >= 0; $bulanKe--) {
            $periode = Carbon::now()->subMonths($bulanKe);

            foreach ($pegawaiList as $pegawai) {
                // skor acak tapi realistis: kebanyakan di rentang 75-95
                $kedisiplinan = fake()->numberBetween(70, 98);
                $kualitas = fake()->numberBetween(70, 98);
                $kerjasama = fake()->numberBetween(70, 98);
                $skorAkhir = round(($kedisiplinan + $kualitas + $kerjasama) / 3, 2);

                $penyetuju = fake()->randomElement([$manajerLayanan, $manajerSdm, $manajerKeuangan]);

                PenilaianKerja::updateOrCreate(
                    [
                        'pegawai_id' => $pegawai->id,
                        'periode_bulan' => $periode->month,
                        'periode_tahun' => $periode->year,
                    ],
                    [
                        'dinilai_oleh' => $penyetuju?->id,
                        'skor_kedisiplinan' => $kedisiplinan,
                        'skor_kualitas_kerja' => $kualitas,
                        'skor_kerjasama' => $kerjasama,
                        'skor_akhir' => $skorAkhir,
                        'catatan' => $skorAkhir >= 85 ? 'Kinerja sangat baik' : 'Kinerja baik, perlu ditingkatkan di beberapa aspek',
                    ]
                );
            }
        }
    }
}