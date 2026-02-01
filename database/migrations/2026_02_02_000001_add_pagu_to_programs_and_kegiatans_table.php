<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cek & Update Tabel Programs
        // Kita cek dulu agar tidak error jika kolom sudah terlanjur terbuat di percobaan sebelumnya
        if (!Schema::hasColumn('programs', 'pagu_anggaran')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->decimal('pagu_anggaran', 15, 2)->default(0)->after('nama');
            });
        }

        // 2. Cek & Update Tabel Kegiatans
        if (!Schema::hasColumn('kegiatans', 'pagu_anggaran')) {
            Schema::table('kegiatans', function (Blueprint $table) {
                // REVISI: Menggunakan after('nama') karena kolom 'output' tidak ditemukan
                $table->decimal('pagu_anggaran', 15, 2)->default(0)->after('nama');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('programs', 'pagu_anggaran')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('pagu_anggaran');
            });
        }

        if (Schema::hasColumn('kegiatans', 'pagu_anggaran')) {
            Schema::table('kegiatans', function (Blueprint $table) {
                $table->dropColumn('pagu_anggaran');
            });
        }
    }
};