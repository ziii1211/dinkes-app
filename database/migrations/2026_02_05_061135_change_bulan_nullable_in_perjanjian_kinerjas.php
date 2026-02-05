<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
            // Ubah kolom 'bulan' agar BOLEH KOSONG (NULL)
            // Ini penting agar PK bisa diset sebagai "Tahunan" (tanpa bulan spesifik)
            $table->tinyInteger('bulan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
            // Kembalikan ke pengaturan awal (tidak boleh null, default 1)
            $table->tinyInteger('bulan')->default(1)->change();
        });
    }
};