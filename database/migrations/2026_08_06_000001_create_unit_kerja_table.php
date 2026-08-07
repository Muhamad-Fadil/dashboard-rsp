<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit');           // contoh: IGD, Poli Umum, Bagian Keuangan
            $table->string('kode_unit')->unique();  // contoh: IGD, POLI-UM, KEU-01
            $table->foreignId('division_id')        // unit ini masuk divisi mana (Layanan/SDM/Keuangan)
                  ->constrained('divisions')
                  ->cascadeOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_kerja');
    }
};