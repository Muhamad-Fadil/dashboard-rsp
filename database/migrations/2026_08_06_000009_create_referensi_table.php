<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referensi', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');   // contoh: jenis_pembayaran, agama, golongan_darah
            $table->string('kode');       // contoh: bpjs, umum, tunai
            $table->string('nilai');      // contoh: "BPJS", "Umum", "Tunai" (yang ditampilkan ke user)
            $table->integer('urutan')->default(0); // buat atur urutan tampil di dropdown
            $table->timestamps();

            $table->unique(['kategori', 'kode']); // 1 kode unik per kategori
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referensi');
    }
};