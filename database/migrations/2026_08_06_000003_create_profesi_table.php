<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_profesi'); // contoh: Dokter Umum, Dokter Spesialis, Perawat, Apoteker, Radiografer, Administrasi

            // kategori ini yang dipakai buat grafik komposisi SDM (Dokter/Perawat/Nakes lain/Nonkes)
            $table->enum('kategori', [
                'medis',            // dokter umum & spesialis
                'keperawatan',      // perawat & bidan
                'nakes_lain',       // apoteker, analis, radiografer, dll
                'nonkesehatan',     // administrasi, umum, pendukung
            ]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesi');
    }
};