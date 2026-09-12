<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn('diagnosa');
        });

        Schema::table('pasien', function (Blueprint $table) {
            $table->dropForeign(['wilayah_bogor_id']);
            $table->dropColumn('wilayah_bogor_id');
        });

        Schema::dropIfExists('wilayah_bogor');
    }

    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->string('diagnosa')->nullable();
        });

        Schema::create('wilayah_bogor', function (Blueprint $table) {
            $table->id();
            $table->string('kode_wilayah')->unique();
            $table->string('nama_kecamatan');
            $table->enum('kabupaten_kota', ['kabupaten', 'kota']);
            $table->timestamps();
        });

        Schema::table('pasien', function (Blueprint $table) {
            $table->foreignId('wilayah_bogor_id')->nullable()->constrained('wilayah_bogor')->nullOnDelete();
        });
    }
};