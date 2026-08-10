<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE kamar MODIFY kelas ENUM('vip','kelas_1','kelas_2','kelas_3','icu','hcu') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE kamar MODIFY kelas ENUM('vip','kelas_1','kelas_2','kelas_3','icu') NOT NULL");
    }
};