<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. BERSIHKAN DATA (Wajib agar tidak error "Integrity constraint")
        // Data lama harus dihapus karena struktur induknya berubah total.
        DB::table('indikator_kegiatans')->truncate();

        Schema::table('indikator_kegiatans', function (Blueprint $table) {
            
            // 2. CEK & HAPUS KOLOM LAMA (Hanya jika kolomnya masih ada)
            if (Schema::hasColumn('indikator_kegiatans', 'kegiatan_id')) {
                
                // Coba hapus Foreign Key dulu (gunakan try-catch agar tidak stop jika sudah hilang)
                // Kita cek manual lewat nama constraint standar Laravel
                $fkName = 'indikator_kegiatans_kegiatan_id_foreign';
                $hasFk = DB::select(
                    "SELECT CONSTRAINT_NAME 
                     FROM information_schema.KEY_COLUMN_USAGE 
                     WHERE TABLE_NAME = 'indikator_kegiatans' 
                     AND CONSTRAINT_NAME = '$fkName' 
                     AND TABLE_SCHEMA = DATABASE()"
                );

                if (!empty($hasFk)) {
                    $table->dropForeign($fkName);
                }
                
                // Hapus kolom
                $table->dropColumn('kegiatan_id');
            }

            // 3. TAMBAH KOLOM BARU (Hanya jika belum ada)
            if (!Schema::hasColumn('indikator_kegiatans', 'output_kegiatan_id')) {
                $table->foreignId('output_kegiatan_id')
                      ->after('id')
                      ->constrained('output_kegiatans')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        // Rollback logic (Disederhanakan)
        DB::table('indikator_kegiatans')->truncate();
        
        Schema::table('indikator_kegiatans', function (Blueprint $table) {
            if (Schema::hasColumn('indikator_kegiatans', 'output_kegiatan_id')) {
                // Cek FK sebelum drop (opsional, tapi aman)
                $table->dropForeign(['output_kegiatan_id']);
                $table->dropColumn('output_kegiatan_id');
            }
            
            if (!Schema::hasColumn('indikator_kegiatans', 'kegiatan_id')) {
                $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');
            }
        });
    }
};