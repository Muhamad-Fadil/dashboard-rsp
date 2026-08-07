<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_shift', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pegawai_id')->constrained('pegawai')->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained('shift')->cascadeOnDelete();

            $table->date('tanggal');
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->unique(['pegawai_id', 'tanggal']); // 1 pegawai cuma bisa 1 jadwal per hari
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift');
    }
};