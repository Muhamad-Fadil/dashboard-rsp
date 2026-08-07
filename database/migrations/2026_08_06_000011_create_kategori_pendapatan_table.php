<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_pendapatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori'); // contoh: Pendapatan Rawat Jalan, Pendapatan Rawat Inap, Pendapatan BPJS
            $table->string('kode')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_pendapatan');
    }
};