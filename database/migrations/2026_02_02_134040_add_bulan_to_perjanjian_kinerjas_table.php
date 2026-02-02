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
        // Cek dulu apakah kolom 'bulan' SUDAH ADA?
        if (!Schema::hasColumn('perjanjian_kinerjas', 'bulan')) {
            Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
                // Jika belum ada, baru buat kolomnya
                $table->tinyInteger('bulan')->after('tahun')->default(1);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('perjanjian_kinerjas', 'bulan')) {
            Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
                $table->dropColumn('bulan');
            });
        }
    }
};
