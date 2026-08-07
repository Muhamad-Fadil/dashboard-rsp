<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique(); // Nomor Induk Pegawai
            $table->string('nama');
            $table->foreignId('profesi_id')->constrained('profesi')->cascadeOnDelete();
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja')->cascadeOnDelete();

            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir')->nullable();
            $table->date('tanggal_masuk'); // dipakai hitung masa kerja

            $table->enum('status_kepegawaian', ['tetap', 'kontrak', 'honorer'])->default('tetap');

            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();

            // opsional: kalau pegawai ini juga punya akun login di sistem (Manajer/Operator/dst)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};