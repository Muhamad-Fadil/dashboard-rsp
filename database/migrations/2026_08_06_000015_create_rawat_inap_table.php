<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rawat_inap', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kunjungan_id')->constrained('kunjungan')->cascadeOnDelete();
            $table->foreignId('bed_id')->constrained('bed')->cascadeOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();

            // dua kolom ini WAJIB diisi lengkap — sumber utama hitung ALOS, TOI, BTO
            $table->dateTime('tanggal_masuk');
            $table->dateTime('tanggal_keluar')->nullable(); // null selama pasien masih dirawat

            $table->enum('status', ['dirawat', 'pulang', 'rujuk', 'meninggal'])->default('dirawat');
            $table->text('diagnosa')->nullable();
            $table->text('catatan_keluar')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rawat_inap');
    }
};