<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\KlaimBpjs;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KlaimBpjsSeeder extends Seeder
{
    public function run(): void
    {
        // ambil ~25% dari kunjungan yang sudah selesai, dianggap pasien BPJS
        $kunjunganSelesai = Kunjungan::where('status', 'selesai')
            ->inRandomOrder()
            ->limit((int) (Kunjungan::where('status', 'selesai')->count() * 0.25))
            ->get();

        $nomorUrut = 1;

        foreach ($kunjunganSelesai as $k) {
            $tanggalPengajuan = Carbon::parse($k->waktu_selesai ?? $k->waktu_daftar);
            $jumlahKlaim = fake()->numberBetween(500_000, 8_000_000);

            $status = fake()->randomElement(['diajukan', 'diverifikasi', 'disetujui', 'disetujui', 'dibayar', 'dibayar']);
            $sudahDiproses = in_array($status, ['disetujui', 'dibayar']);

            KlaimBpjs::create([
                'pasien_id' => $k->pasien_id,
                'kunjungan_id' => $k->id,
                'no_sep' => 'SEP-' . $tanggalPengajuan->format('Ymd') . '-' . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT),
                'jumlah_klaim' => $jumlahKlaim,
                'jumlah_disetujui' => $sudahDiproses ? round($jumlahKlaim * fake()->randomFloat(2, 0.9, 1.0)) : null,
                'tanggal_pengajuan' => $tanggalPengajuan,
                'tanggal_disetujui' => $sudahDiproses ? $tanggalPengajuan->copy()->addDays(fake()->numberBetween(3, 14)) : null,
                'status' => $status,
                'keterangan' => 'Klaim BPJS Kesehatan',
            ]);

            $nomorUrut++;
        }
    }
}