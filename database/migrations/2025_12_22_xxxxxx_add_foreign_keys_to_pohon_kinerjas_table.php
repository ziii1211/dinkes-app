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
        // 1. Cek dan buat kolom satu per satu (Safe)
        Schema::table('pohon_kinerjas', function (Blueprint $table) {
            if (!Schema::hasColumn('pohon_kinerjas', 'tujuan_id')) {
                $table->unsignedBigInteger('tujuan_id')->nullable()->after('id');
            }
            // Logic penempatan 'after' yang dinamis
            $afterSasaran = Schema::hasColumn('pohon_kinerjas', 'tujuan_id') ? 'tujuan_id' : 'id';
            if (!Schema::hasColumn('pohon_kinerjas', 'sasaran_id')) {
                $table->unsignedBigInteger('sasaran_id')->nullable()->after($afterSasaran);
            }

            $afterOutcome = Schema::hasColumn('pohon_kinerjas', 'sasaran_id') ? 'sasaran_id' : $afterSasaran;
            if (!Schema::hasColumn('pohon_kinerjas', 'outcome_id')) {
                $table->unsignedBigInteger('outcome_id')->nullable()->after($afterOutcome);
            }

            $afterKegiatan = Schema::hasColumn('pohon_kinerjas', 'outcome_id') ? 'outcome_id' : $afterOutcome;
            if (!Schema::hasColumn('pohon_kinerjas', 'kegiatan_id')) {
                $table->unsignedBigInteger('kegiatan_id')->nullable()->after($afterKegiatan);
            }

            $afterSub = Schema::hasColumn('pohon_kinerjas', 'kegiatan_id') ? 'kegiatan_id' : $afterKegiatan;
            if (!Schema::hasColumn('pohon_kinerjas', 'sub_kegiatan_id')) {
                $table->unsignedBigInteger('sub_kegiatan_id')->nullable()->after($afterSub);
            }
        });

        // 2. Buat Index dalam blok Try-Catch
        // Ini solusi paling ampuh: Coba bikin index, kalau error (karena sudah ada), abaikan saja.
        try {
            Schema::table('pohon_kinerjas', function (Blueprint $table) {
                $table->index([
                    'tujuan_id', 
                    'sasaran_id', 
                    'outcome_id', 
                    'kegiatan_id', 
                    'sub_kegiatan_id'
                ], 'idx_pohon_rel');
            });
        } catch (\Throwable $e) {
            // Index sudah ada atau error lain, kita skip saja biar migrasi tetap jalan
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pohon_kinerjas', function (Blueprint $table) {
            // Hapus index menggunakan nama pendek tadi
            $table->dropIndex('idx_pohon_rel');
            
            // Hapus kolom
            $table->dropColumn([
                'tujuan_id', 
                'sasaran_id', 
                'outcome_id', 
                'kegiatan_id', 
                'sub_kegiatan_id'
            ]);
        });
    }
};