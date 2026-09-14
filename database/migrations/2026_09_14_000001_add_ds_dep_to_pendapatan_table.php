<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendapatan', function (Blueprint $table) {
            $table->string('ds_dep')->nullable()->after('kunjungan_id');
        });
    }

    public function down(): void
    {
        Schema::table('pendapatan', function (Blueprint $table) {
            $table->dropColumn('ds_dep');
        });
    }
};