<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Status kepegawaian rumah sakit pemerintah: PNS, PPPK, Kontrak BLU
        // (sebelumnya tetap/kontrak/honorer — nggak dipakai buat kebutuhan ini)
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_kepegawaian ENUM('pns', 'pppk', 'kontrak_blu') NOT NULL DEFAULT 'kontrak_blu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pegawai MODIFY COLUMN status_kepegawaian ENUM('tetap', 'kontrak', 'honorer') NOT NULL DEFAULT 'tetap'");
    }
};