<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kamar_id')->constrained('kamar')->cascadeOnDelete();
            $table->string('nomor_bed'); // contoh: A, B, 1, 2

            // status ini yang dipakai hitung BOR (bed terisi vs tersedia saat ini)
            $table->enum('status', ['tersedia', 'terisi', 'maintenance'])->default('tersedia');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bed');
    }
};