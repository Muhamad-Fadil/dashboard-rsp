<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendapatan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kategori_pendapatan_id')->constrained('kategori_pendapatan')->cascadeOnDelete();
            $table->foreignId('unit_kerja_id')->nullable()->constrained('unit_kerja')->nullOnDelete(); // pendapatan per unit layanan

            // opsional: kalau pendapatan ini nyambung ke kunjungan pasien tertentu
            $table->foreignId('kunjungan_id')->nullable()->constrained('kunjungan')->nullOnDelete();

            $table->date('tanggal');
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // operator yang input

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendapatan');
    }
};