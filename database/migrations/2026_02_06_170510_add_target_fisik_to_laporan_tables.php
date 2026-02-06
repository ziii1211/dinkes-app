<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    // 1. Tambah kolom ke tabel Anggaran (Untuk Program & Kegiatan)
    Schema::table('laporan_konsolidasi_anggarans', function (Blueprint $table) {
        // Cek dulu biar tidak error kalau sudah ada
        if (!Schema::hasColumn('laporan_konsolidasi_anggarans', 'target')) {
            $table->float('target')->default(0)->after('pagu_realisasi');
        }
        if (!Schema::hasColumn('laporan_konsolidasi_anggarans', 'realisasi_fisik')) {
            $table->float('realisasi_fisik')->default(0)->after('target');
        }
    });

    // 2. Tambah kolom ke tabel Detail (Untuk Sub Kegiatan)
    Schema::table('detail_laporan_konsolidasis', function (Blueprint $table) {
        if (!Schema::hasColumn('detail_laporan_konsolidasis', 'target')) {
            $table->float('target')->default(0)->after('satuan_unit'); // Sesuaikan posisi
        }
        if (!Schema::hasColumn('detail_laporan_konsolidasis', 'realisasi_fisik')) {
            $table->float('realisasi_fisik')->default(0)->after('pagu_realisasi');
        }
    });
}

public function down()
{
    Schema::table('laporan_konsolidasi_anggarans', function (Blueprint $table) {
        $table->dropColumn(['target', 'realisasi_fisik']);
    });
    Schema::table('detail_laporan_konsolidasis', function (Blueprint $table) {
        $table->dropColumn(['target', 'realisasi_fisik']);
    });
}
};
