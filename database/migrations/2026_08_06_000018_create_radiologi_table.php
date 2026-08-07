<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiologi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kunjungan_id')->constrained('kunjungan')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('jenis_pemeriksaan'); // contoh: Rontgen Thorax, CT Scan, USG

            $table->dateTime('waktu_periksa');
            $table->text('hasil')->nullable();

            $table->enum('status', ['menunggu', 'diproses', 'selesai'])->default('menunggu');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiologi');
    }
};