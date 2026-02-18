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
        // 1. Tambah tahun di tabel Programs
        Schema::table('programs', function (Blueprint $table) {
            $table->year('tahun')->default(2026)->after('kode');
            // Index biar pencarian per tahun cepat
            $table->index('tahun'); 
        });

        // 2. Tambah tahun di tabel Kegiatans
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->year('tahun')->default(2026)->after('program_id');
            $table->index('tahun');
        });

        // 3. Tambah tahun di tabel Sub Kegiatans
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->year('tahun')->default(2026)->after('kegiatan_id');
            $table->index('tahun');
        });

        // 4. Tambah tahun di tabel Indikator Sub Kegiatans
        Schema::table('indikator_sub_kegiatans', function (Blueprint $table) {
            $table->year('tahun')->default(2026)->after('sub_kegiatan_id');
            $table->index('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_sub_kegiatans', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });

        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });

        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('tahun');
        });
    }
};