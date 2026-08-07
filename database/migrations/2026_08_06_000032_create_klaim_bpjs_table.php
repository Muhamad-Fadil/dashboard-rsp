<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klaim_bpjs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
            $table->foreignId('kunjungan_id')->nullable()->constrained('kunjungan')->nullOnDelete();

            $table->string('no_sep')->nullable(); // Surat Eligibilitas Peserta BPJS
            $table->decimal('jumlah_klaim', 15, 2);
            $table->decimal('jumlah_disetujui', 15, 2)->nullable(); // hasil verifikasi BPJS, bisa beda dari yg diajukan

            $table->date('tanggal_pengajuan');
            $table->date('tanggal_disetujui')->nullable();

            $table->enum('status', ['diajukan', 'diverifikasi', 'disetujui', 'ditolak', 'dibayar'])->default('diajukan');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaim_bpjs');
    }
};