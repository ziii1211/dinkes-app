<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('renstra_settings', function (Blueprint $table) {
            // Kita tambahkan pengecekan agar tidak error jika kolom sudah ada
            
            if (!Schema::hasColumn('renstra_settings', 'periode')) {
                $table->string('periode')->nullable()->after('id');
            }
            
            if (!Schema::hasColumn('renstra_settings', 'tahun_awal')) {
                $table->integer('tahun_awal')->nullable()->after('periode');
            }
            
            if (!Schema::hasColumn('renstra_settings', 'tahun_akhir')) {
                $table->integer('tahun_akhir')->nullable()->after('tahun_awal');
            }

            if (!Schema::hasColumn('renstra_settings', 'is_aktif')) {
                $table->boolean('is_aktif')->default(true)->after('tahun_akhir');
            }

            // Ini kolom utama yang kita butuhkan untuk file PDF
            if (!Schema::hasColumn('renstra_settings', 'file_path')) {
                $table->string('file_path')->nullable()->after('is_aktif');
            }
        });
    }

    public function down(): void
    {
        Schema::table('renstra_settings', function (Blueprint $table) {
            $table->dropColumn(['periode', 'tahun_awal', 'tahun_akhir', 'is_aktif', 'file_path']);
        });
    }
};