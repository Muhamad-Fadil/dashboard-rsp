<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Tabel 1: field data pegawai yang masih kurang
            $table->string('nik', 20)->nullable()->unique()->after('nip');
            $table->string('tempat_lahir')->nullable()->after('tanggal_lahir');
            $table->string('pendidikan')->nullable()->after('status_kepegawaian'); // contoh: S1, D3, SMA
            $table->string('jabatan')->nullable()->after('pendidikan'); // posisi struktural, beda dari profesi
            $table->string('golongan')->nullable()->after('jabatan'); // contoh: III/a, PK II

            // Tabel 2: penanda pegawai ini ikut jadwal shift bergilir atau kerja jam tetap (non-shift)
            $table->enum('jenis_kerja', ['shift', 'non_shift'])->default('shift')->after('golongan');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn(['nik', 'tempat_lahir', 'pendidikan', 'jabatan', 'golongan', 'jenis_kerja']);
        });
    }
};
