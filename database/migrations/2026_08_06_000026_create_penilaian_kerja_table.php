<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_kerja', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('dinilai_oleh')->nullable()->constrained('users')->nullOnDelete(); // biasanya Manajer

            $table->integer('periode_bulan'); // 1-12
            $table->integer('periode_tahun');

            // skor per aspek, skala 1-100, biar bisa dianalisa per kategori
            $table->decimal('skor_kedisiplinan', 5, 2)->nullable();
            $table->decimal('skor_kualitas_kerja', 5, 2)->nullable();
            $table->decimal('skor_kerjasama', 5, 2)->nullable();
            $table->decimal('skor_akhir', 5, 2)->nullable(); // rata-rata / hasil akhir

            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique(['pegawai_id', 'periode_bulan', 'periode_tahun']); // 1 pegawai cuma 1 penilaian per bulan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kerja');
    }
};