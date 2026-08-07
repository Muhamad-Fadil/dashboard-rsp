<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_pengeluaran_id')->constrained('kategori_pengeluaran')->cascadeOnDelete();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerja')->nullOnDelete();

            $table->integer('tahun');
            $table->integer('bulan')->nullable(); // null kalau anggaran-nya per tahun, bukan per bulan

            $table->decimal('jumlah_anggaran', 15, 2);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique(['kategori_pengeluaran_id', 'unit_kerja_id', 'tahun', 'bulan'], 'anggaran_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggaran');
    }
};