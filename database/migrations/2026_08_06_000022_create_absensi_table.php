<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('jadwal_shift_id')->nullable()->constrained('jadwal_shift')->nullOnDelete();

            $table->date('tanggal');
            $table->dateTime('jam_masuk')->nullable();
            $table->dateTime('jam_pulang')->nullable();

            // status ini yang langsung dipakai hitung persentase kehadiran
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->default('hadir');

            $table->timestamps();

            $table->unique(['pegawai_id', 'tanggal']); // 1 pegawai cuma 1 record absensi per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};