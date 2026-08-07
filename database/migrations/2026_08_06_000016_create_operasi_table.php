<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kunjungan_id')->constrained('kunjungan')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokter')->cascadeOnDelete(); // dokter bedah utama

            $table->string('jenis_operasi');
            $table->string('ruang_operasi')->nullable();

            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai')->nullable();

            $table->enum('status', ['dijadwalkan', 'berlangsung', 'selesai', 'batal'])->default('dijadwalkan');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operasi');
    }
};