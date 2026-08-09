<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\Cuti;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CutiSeeder extends Seeder
{
    public function run(): void
    {
        // ambil manajer tiap divisi buat jadi "penyetuju"
        $manajerLayanan = User::where('email', 'manajer.layanan@rspgoenawan.co.id')->first();
        $manajerSdm = User::where('email', 'manajer.sdm@rspgoenawan.co.id')->first();
        $manajerKeuangan = User::where('email', 'manajer.keuangan@rspgoenawan.co.id')->first();

        // ambil ~8 pegawai acak buat dikasih riwayat cuti (nggak semua pegawai perlu cuti)
        $pegawaiList = Pegawai::inRandomOrder()->limit(8)->get();

        $jenisCuti = ['tahunan', 'tahunan', 'sakit', 'izin_khusus', 'tahunan', 'melahirkan'];
        $alasanByJenis = [
            'tahunan' => 'Cuti tahunan keperluan keluarga',
            'sakit' => 'Sakit, ada surat dokter',
            'izin_khusus' => 'Keperluan mendesak keluarga',
            'melahirkan' => 'Cuti melahirkan',
            'tanpa_gaji' => 'Keperluan pribadi mendesak',
        ];

        foreach ($pegawaiList as $index => $pegawai) {
            $jenis = fake()->randomElement($jenisCuti);
            $lamaHari = $jenis === 'melahirkan' ? 90 : fake()->numberBetween(1, 5);

            // tanggal mulai cuti tersebar di 2 bulan terakhir
            $mulai = Carbon::now()->subDays(fake()->numberBetween(5, 60));
            $selesai = $mulai->copy()->addDays($lamaHari - 1);

            // penyetuju sesuai unit kerja pegawai kira-kira di divisi mana (sederhana: acak dari 3 manajer)
            $penyetuju = fake()->randomElement([$manajerLayanan, $manajerSdm, $manajerKeuangan]);

            Cuti::create([
                'pegawai_id' => $pegawai->id,
                'jenis_cuti' => $jenis,
                'tanggal_mulai' => $mulai->format('Y-m-d'),
                'tanggal_selesai' => $selesai->format('Y-m-d'),
                'jumlah_hari' => $lamaHari,
                'alasan' => $alasanByJenis[$jenis],
                'status' => $selesai->isPast() ? 'disetujui' : fake()->randomElement(['disetujui', 'diajukan']),
                'disetujui_oleh' => $penyetuju?->id,
            ]);
        }
    }
}