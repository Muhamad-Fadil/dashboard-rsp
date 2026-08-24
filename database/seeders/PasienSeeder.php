<?php

namespace Database\Seeders;

use App\Models\Pasien;
use App\Models\Referensi;
use App\Models\WilayahBogor;
use Illuminate\Database\Seeder;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        // ambil id referensi jenis_pembayaran, dengan bobot: BPJS paling banyak (mayoritas pasien RS)
        $jenisPembayaranIds = Referensi::where('kategori', 'jenis_pembayaran')->pluck('id', 'kode');

        $wilayahIds = WilayahBogor::pluck('id')->all();

        $pilihanBobot = [
            ...array_fill(0, 55, $jenisPembayaranIds['bpjs']),
            ...array_fill(0, 35, $jenisPembayaranIds['umum']),
            ...array_fill(0, 10, $jenisPembayaranIds['asuransi']),
        ];

            for ($i = 1; $i <= 40; $i++) {
            $noRm = 'RM-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $tanggalRegistrasi = now()->subDays(rand(0, 180));
            $noRegistrasi = 'REG-' . $tanggalRegistrasi->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $jk = fake()->randomElement(['L', 'P']);

            Pasien::updateOrCreate(
                ['no_rm' => $noRm],
                [
                    'no_registrasi' => $noRegistrasi,
                    'tanggal_registrasi' => $tanggalRegistrasi->format('Y-m-d'),
                    'nama' => $jk === 'L' ? fake('id_ID')->name('male') : fake('id_ID')->name('female'),
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => fake()->dateTimeBetween('-75 years', '-1 year')->format('Y-m-d'),
                    'alamat' => fake('id_ID')->address(),
                    'no_hp' => '08' . fake()->numerify('##########'),
                    'nik' => fake()->numerify('################'),
                    'jenis_pembayaran_id' => fake()->randomElement($pilihanBobot),
                    'wilayah_bogor_id' => fake()->randomElement($wilayahIds),
                ]
            );
        }
    }
}