<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->foreignId('wilayah_bogor_id')->nullable()->after('alamat')
                  ->constrained('wilayah_bogor')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropForeign(['wilayah_bogor_id']);
            $table->dropColumn('wilayah_bogor_id');
        });
    }
};