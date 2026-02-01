<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Tabel Programs
        if (!Schema::hasColumn('programs', 'pagu_realisasi')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->decimal('pagu_realisasi', 15, 2)->default(0)->after('pagu_anggaran');
            });
        }

        // 2. Update Tabel Kegiatans
        if (!Schema::hasColumn('kegiatans', 'pagu_realisasi')) {
            Schema::table('kegiatans', function (Blueprint $table) {
                $table->decimal('pagu_realisasi', 15, 2)->default(0)->after('pagu_anggaran');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('programs', 'pagu_realisasi')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->dropColumn('pagu_realisasi');
            });
        }

        if (Schema::hasColumn('kegiatans', 'pagu_realisasi')) {
            Schema::table('kegiatans', function (Blueprint $table) {
                $table->dropColumn('pagu_realisasi');
            });
        }
    }
};