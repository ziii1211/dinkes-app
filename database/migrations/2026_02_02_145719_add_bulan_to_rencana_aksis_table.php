<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('rencana_aksis', 'bulan')) {
            Schema::table('rencana_aksis', function (Blueprint $table) {
                // Tambah kolom bulan, default 1 (Januari) untuk data lama
                $table->tinyInteger('bulan')->after('tahun')->default(1);
                
                // Tambah index agar pencarian per bulan cepat
                $table->index(['jabatan_id', 'bulan', 'tahun']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rencana_aksis', 'bulan')) {
            Schema::table('rencana_aksis', function (Blueprint $table) {
                $table->dropIndex(['jabatan_id', 'bulan', 'tahun']);
                $table->dropColumn('bulan');
            });
        }
    }
};