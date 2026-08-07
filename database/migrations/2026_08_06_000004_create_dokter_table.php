<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokter', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('profesi_id')->constrained('profesi')->cascadeOnDelete();
            $table->foreignId('poli_id')->nullable()->constrained('poli')->nullOnDelete(); // poli tempat praktik utama, boleh kosong (misal dokter IGD)
            $table->string('no_str')->nullable(); // Nomor Surat Tanda Registrasi
            $table->string('no_hp')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokter');
    }
};