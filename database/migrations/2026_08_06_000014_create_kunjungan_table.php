<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->string('no_kunjungan')->unique(); // nomor antrian/registrasi kunjungan

            $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
            $table->foreignId('poli_id')->nullable()->constrained('poli')->nullOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();

            $table->enum('jenis_kunjungan', ['rawat_jalan', 'rawat_inap', 'igd']);
            $table->text('keluhan')->nullable();

            $table->enum('status', ['menunggu', 'dilayani', 'selesai', 'batal'])->default('menunggu');

            // dipakai buat hitung "waktu tunggu pelayanan"
            $table->dateTime('waktu_daftar');
            $table->dateTime('waktu_dilayani')->nullable();
            $table->dateTime('waktu_selesai')->nullable();

            // operator yang menginput data ini
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};