<?php

namespace Database\Seeders;

use App\Models\Pasien;
use Illuminate\Database\Seeder;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 40; $i++) {
            $noRm = 'RM-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $jk = fake()->randomElement(['L', 'P']);

            Pasien::updateOrCreate(
                ['no_rm' => $noRm],
                [
                    'nama' => $jk === 'L' ? fake('id_ID')->name('male') : fake('id_ID')->name('female'),
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => fake()->dateTimeBetween('-75 years', '-1 year')->format('Y-m-d'),
                    'alamat' => fake('id_ID')->address(),
                    'no_hp' => '08' . fake()->numerify('##########'),
                    'nik' => fake()->numerify('################'),
                ]
            );
        }
    }
}