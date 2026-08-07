<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_pelatihan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('pelatihan_id')->constrained('pelatihan')->cascadeOnDelete();

            $table->enum('status', ['terdaftar', 'mengikuti', 'selesai', 'tidak_hadir'])->default('terdaftar');
            $table->string('sertifikat')->nullable(); // path/nama file sertifikat kalau ada

            $table->timestamps();

            $table->unique(['pegawai_id', 'pelatihan_id']); // 1 pegawai cuma bisa 1 kali daftar ke pelatihan yang sama
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_pelatihan');
    }
};