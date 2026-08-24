<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_bogor', function (Blueprint $table) {
            $table->id();
            $table->string('kode_wilayah')->unique(); // kode resmi Kemendagri, contoh: 32.01.25
            $table->string('nama_kecamatan');
            $table->enum('kabupaten_kota', ['kabupaten', 'kota']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_bogor');
    }
};