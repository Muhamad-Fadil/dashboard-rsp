<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operator_submenu_akses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // slug sub-menu, contoh: pasien, kunjungan, rawat-inap, operasi, laboratorium, radiologi, resep
            $table->string('submenu');

            $table->timestamps();

            $table->unique(['user_id', 'submenu']); // nggak boleh dobel centang submenu yang sama
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_submenu_akses');
    }
};