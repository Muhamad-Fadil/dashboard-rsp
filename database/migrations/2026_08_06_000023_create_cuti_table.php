<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();

            $table->enum('jenis_cuti', ['tahunan', 'sakit', 'melahirkan', 'izin_khusus', 'tanpa_gaji']);

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('jumlah_hari'); // dihitung otomatis saat input, disimpan biar query cepat

            $table->text('alasan')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak'])->default('diajukan');

            // yang menyetujui cuti (biasanya Manajer divisi terkait)
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};