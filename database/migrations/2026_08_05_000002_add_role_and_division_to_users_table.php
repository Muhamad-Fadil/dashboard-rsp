<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // enum role: direktur & admin tidak terikat divisi (division_id boleh null)
            // manajer & operator wajib punya division_id
            $table->enum('role', ['admin', 'direktur', 'manajer', 'operator'])
                  ->default('operator')
                  ->after('email');

            $table->foreignId('division_id')
                  ->nullable()
                  ->after('role')
                  ->constrained('divisions')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn(['role', 'division_id']);
        });
    }
};
