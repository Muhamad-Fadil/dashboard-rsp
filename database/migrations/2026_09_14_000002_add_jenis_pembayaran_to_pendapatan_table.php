<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendapatan', function (Blueprint $table) {
            if (! Schema::hasColumn('pendapatan', 'ds_dep')) {
                $table->string('ds_dep')->nullable()->after('kunjungan_id');
            }
            if (! Schema::hasColumn('pendapatan', 'jenis_pembayaran')) {
                $table->string('jenis_pembayaran')->nullable()->after('ds_dep');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendapatan', function (Blueprint $table) {
            $table->dropColumn(['ds_dep', 'jenis_pembayaran']);
        });
    }
};