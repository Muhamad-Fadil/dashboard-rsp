<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\RawatInap;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RawatInapSeeder extends Seeder
{
    public function run(): void
    {
        $kunjunganRanap = Kunjungan::where('jenis_kunjungan', 'rawat_inap')->get();
        $bedIds = Bed::pluck('id')->all();
        $dokterIds = Dokter::pluck('id')->all();

        foreach ($kunjunganRanap as $index => $kunjungan) {
            $tanggalMasuk = $kunjungan->waktu_daftar->copy();

            // ~20% pasien masih dirawat sekarang (tanggal_keluar null), sisanya sudah pulang
            $masihDirawat = $tanggalMasuk->gt(Carbon::now()->subDays(7)) && fake()->boolean(60);

            $lamaRawat = fake()->numberBetween(1, 8); // 1-8 hari rawat
            $tanggalKeluar = $masihDirawat ? null : $tanggalMasuk->copy()->addDays($lamaRawat);

            $bedId = fake()->randomElement($bedIds);

            RawatInap::create([
                'kunjungan_id' => $kunjungan->id,
                'bed_id' => $bedId,
                'dokter_id' => fake()->randomElement($dokterIds),
                'tanggal_masuk' => $tanggalMasuk,
                'tanggal_keluar' => $tanggalKeluar,
                'status' => $masihDirawat ? 'dirawat' : fake()->randomElement(['pulang', 'pulang', 'pulang', 'rujuk']),
                'diagnosa' => fake('id_ID')->randomElement([
                    'Observasi febris', 'Gastroenteritis akut', 'Post operasi apendiktomi',
                    'Hipertensi tidak terkontrol', 'Diabetes mellitus tipe 2', 'Dengue fever',
                    'Pneumonia', 'Anemia',
                ]),
                'catatan_keluar' => $masihDirawat ? null : 'Kondisi membaik, pasien pulang',
            ]);

            // kalau masih dirawat, update status bed jadi "terisi"
            if ($masihDirawat) {
                Bed::where('id', $bedId)->update(['status' => 'terisi']);
            }
        }
    }
}