<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\JadwalShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        $jadwalList = JadwalShift::with('shift')->get();

        foreach ($jadwalList as $jadwal) {
            // distribusi status: 92% hadir, 3% terlambat, 2% izin, 2% sakit, 1% alpha
            $status = fake()->randomElement([
                ...array_fill(0, 92, 'hadir'),
                ...array_fill(0, 3, 'terlambat'),
                ...array_fill(0, 2, 'izin'),
                ...array_fill(0, 2, 'sakit'),
                ...array_fill(0, 1, 'alpha'),
            ]);

            $jamMulaiShift = $jadwal->shift->jam_mulai;
            [$jamJadwal, $menitJadwal] = explode(':', $jamMulaiShift);

            $jamMasuk = null;
            $jamPulang = null;

            if (in_array($status, ['hadir', 'terlambat'])) {
                $tambahMenit = $status === 'terlambat' ? fake()->numberBetween(15, 60) : fake()->numberBetween(-10, 5);

                $jamMasuk = Carbon::parse($jadwal->tanggal)
                    ->setTime((int) $jamJadwal, (int) $menitJadwal)
                    ->addMinutes($tambahMenit);

                $jamPulang = $jamMasuk->copy()->addHours(7)->addMinutes(fake()->numberBetween(0, 30));
            }

            Absensi::updateOrCreate(
                ['pegawai_id' => $jadwal->pegawai_id, 'tanggal' => $jadwal->tanggal],
                [
                    'jadwal_shift_id' => $jadwal->id,
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang,
                    'status' => $status,
                ]
            );
        }
    }
}