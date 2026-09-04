<?php

namespace Database\Seeders;

use App\Models\JadwalShift;
use App\Models\Pegawai;
use App\Models\Shift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class JadwalShiftSeeder extends Seeder
{
    public function run(): void
    {
        $pegawaiList = Pegawai::where('aktif', true)->where('jenis_kerja', 'shift')->get();
        $shiftIds = Shift::pluck('id')->all();

        // jadwal buat 14 hari terakhir sampai hari ini
        for ($h = 13; $h >= 0; $h--) {
            $tanggal = Carbon::now()->subDays($h)->format('Y-m-d');

            foreach ($pegawaiList as $pegawai) {
                JadwalShift::updateOrCreate(
                    ['pegawai_id' => $pegawai->id, 'tanggal' => $tanggal],
                    ['shift_id' => fake()->randomElement($shiftIds)]
                );
            }
        }
    }
}
