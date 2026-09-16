<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('klaim_bpjs', function (Blueprint $table) {
            // jenis kepesertaan BPJS -- contoh isi: "BPJS", "BPJS PBI", "BPJS NON PBI",
            // "Askes Sosial", "Jamkesmas" (persis sesuai data sumber, tidak dikarang)
            $table->string('jenis_bpjs')->nullable()->after('no_reg');
        });
    }

    public function down(): void
    {
        Schema::table('klaim_bpjs', function (Blueprint $table) {
            $table->dropColumn('jenis_bpjs');
        });
    }
};