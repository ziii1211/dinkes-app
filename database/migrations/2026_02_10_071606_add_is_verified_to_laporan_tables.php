<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah status verifikasi ke Tabel Anggaran (Program/Kegiatan)
        Schema::table('laporan_konsolidasi_anggarans', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('realisasi_fisik');
        });

        // 2. Tambah status verifikasi ke Tabel Detail (Sub Kegiatan)
        Schema::table('detail_laporan_konsolidasis', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('realisasi_fisik');
        });
    }

    public function down()
    {
        Schema::table('laporan_konsolidasi_anggarans', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
        Schema::table('detail_laporan_konsolidasis', function (Blueprint $table) {
            $table->dropColumn('is_verified');
        });
    }
};