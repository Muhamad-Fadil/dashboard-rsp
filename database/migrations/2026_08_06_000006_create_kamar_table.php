<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kamar');   // contoh: 101, 202, ICU-1
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja')->cascadeOnDelete();

            $table->enum('kelas', ['vip', 'kelas_1', 'kelas_2', 'kelas_3', 'icu']);
            $table->decimal('tarif_per_hari', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};