<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom jabatan_id di tabel output_kegiatans (Cek dulu biar aman jika sudah ada)
        if (!Schema::hasColumn('output_kegiatans', 'jabatan_id')) {
            Schema::table('output_kegiatans', function (Blueprint $table) {
                $table->foreignId('jabatan_id')->nullable()->after('deskripsi')->constrained('jabatans')->onDelete('set null');
            });
        }

        // 2. Migrasikan Data PJ Lama (Hanya jika kolom asal masih ada)
        if (Schema::hasColumn('kegiatans', 'jabatan_id')) {
            $kegiatans = DB::table('kegiatans')->whereNotNull('jabatan_id')->get();
            foreach ($kegiatans as $kegiatan) {
                // Update semua output milik kegiatan tersebut dengan jabatan_id yang sama
                DB::table('output_kegiatans')
                    ->where('kegiatan_id', $kegiatan->id)
                    ->update(['jabatan_id' => $kegiatan->jabatan_id]);
            }
        }

        // 3. Hapus kolom jabatan_id di tabel kegiatans (Dengan pengecekan aman)
        if (Schema::hasColumn('kegiatans', 'jabatan_id')) {
            Schema::table('kegiatans', function (Blueprint $table) {
                
                // Cek keberadaan Foreign Key secara manual via SQL
                // Ini mencegah error "Can't DROP ... check that column/key exists"
                $fkName = 'kegiatans_jabatan_id_foreign'; 
                $hasFk = DB::select(
                    "SELECT CONSTRAINT_NAME 
                     FROM information_schema.KEY_COLUMN_USAGE 
                     WHERE TABLE_NAME = 'kegiatans' 
                     AND CONSTRAINT_NAME = '$fkName' 
                     AND TABLE_SCHEMA = DATABASE()"
                );

                if (!empty($hasFk)) {
                    $table->dropForeign($fkName);
                }
                
                // Hapus kolom
                $table->dropColumn('jabatan_id');
            });
        }
    }

    public function down(): void
    {
        // Rollback: Kembalikan kolom ke tabel kegiatans
        if (!Schema::hasColumn('kegiatans', 'jabatan_id')) {
            Schema::table('kegiatans', function (Blueprint $table) {
                $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->onDelete('set null');
            });
        }

        // Hapus kolom dari tabel output_kegiatans
        if (Schema::hasColumn('output_kegiatans', 'jabatan_id')) {
            Schema::table('output_kegiatans', function (Blueprint $table) {
                // Cek FK sebelum drop
                $fkName = 'output_kegiatans_jabatan_id_foreign';
                $hasFk = DB::select(
                    "SELECT CONSTRAINT_NAME 
                     FROM information_schema.KEY_COLUMN_USAGE 
                     WHERE TABLE_NAME = 'output_kegiatans' 
                     AND CONSTRAINT_NAME = '$fkName' 
                     AND TABLE_SCHEMA = DATABASE()"
                );

                if (!empty($hasFk)) {
                    $table->dropForeign($fkName);
                }
                
                $table->dropColumn('jabatan_id');
            });
        }
    }
};