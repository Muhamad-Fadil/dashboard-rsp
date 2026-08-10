<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            // nyambung ke tabel referensi yang kategori-nya 'jenis_pembayaran' (umum/bpjs/asuransi)
            $table->foreignId('jenis_pembayaran_id')->nullable()->after('nik')
                  ->constrained('referensi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropForeign(['jenis_pembayaran_id']);
            $table->dropColumn('jenis_pembayaran_id');
        });
    }
};