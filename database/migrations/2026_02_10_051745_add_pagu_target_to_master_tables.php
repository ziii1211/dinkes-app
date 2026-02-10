<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah ke Tabel Program
        Schema::table('programs', function (Blueprint $table) {
            // Perbaikan: gunakan 'nama' bukan 'nama_program'
            if (!Schema::hasColumn('programs', 'pagu')) {
                $table->decimal('pagu', 15, 2)->default(0)->after('nama'); 
            }
            if (!Schema::hasColumn('programs', 'target')) {
                $table->float('target')->default(0)->after('pagu');
            }
        });

        // 2. Tambah ke Tabel Kegiatan
        Schema::table('kegiatans', function (Blueprint $table) {
            // Perbaikan: gunakan 'nama' bukan 'nama_kegiatan'
            if (!Schema::hasColumn('kegiatans', 'pagu')) {
                $table->decimal('pagu', 15, 2)->default(0)->after('nama');
            }
            if (!Schema::hasColumn('kegiatans', 'target')) {
                $table->float('target')->default(0)->after('pagu');
            }
        });

        // 3. Tambah ke Tabel Sub Kegiatan
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            // Ini sudah benar pakai 'nama', tapi kita pastikan urutannya
            if (!Schema::hasColumn('sub_kegiatans', 'pagu')) {
                $table->decimal('pagu', 15, 2)->default(0)->after('nama');
            }
            if (!Schema::hasColumn('sub_kegiatans', 'target')) {
                $table->float('target')->default(0)->after('pagu');
            }
        });
    }

    public function down()
    {
        Schema::table('programs', function (Blueprint $table) { 
            $table->dropColumn(['pagu', 'target']); 
        });
        Schema::table('kegiatans', function (Blueprint $table) { 
            $table->dropColumn(['pagu', 'target']); 
        });
        Schema::table('sub_kegiatans', function (Blueprint $table) { 
            $table->dropColumn(['pagu', 'target']); 
        });
    }
};