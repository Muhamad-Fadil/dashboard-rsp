<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rawat_inap', function (Blueprint $table) {
            $table->dropForeign(['bed_id']);
        });

        Schema::table('rawat_inap', function (Blueprint $table) {
            $table->foreignId('bed_id')->nullable()->change();
            $table->foreign('bed_id')->references('id')->on('bed')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rawat_inap', function (Blueprint $table) {
            $table->dropForeign(['bed_id']);
            $table->foreignId('bed_id')->nullable(false)->change();
            $table->foreign('bed_id')->references('id')->on('bed')->cascadeOnDelete();
        });
    }
};