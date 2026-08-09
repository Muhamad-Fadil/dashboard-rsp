<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shift = [
            ['nama_shift' => 'Pagi', 'jam_mulai' => '07:00:00', 'jam_selesai' => '14:00:00'],
            ['nama_shift' => 'Siang', 'jam_mulai' => '14:00:00', 'jam_selesai' => '21:00:00'],
            ['nama_shift' => 'Malam', 'jam_mulai' => '21:00:00', 'jam_selesai' => '07:00:00'],
        ];

        foreach ($shift as $s) {
            Shift::updateOrCreate(['nama_shift' => $s['nama_shift']], $s);
        }
    }
}