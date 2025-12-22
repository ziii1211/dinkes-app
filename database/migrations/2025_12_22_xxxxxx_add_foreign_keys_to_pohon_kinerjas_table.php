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
        Schema::table('pohon_kinerjas', function (Blueprint $table) {
            // Tambah Kolom ID (Cek dulu biar aman kalau dijalankan ulang)
            if (!Schema::hasColumn('pohon_kinerjas', 'tujuan_id')) {
                $table->unsignedBigInteger('tujuan_id')->nullable()->after('id');
                $table->unsignedBigInteger('sasaran_id')->nullable()->after('tujuan_id');
                $table->unsignedBigInteger('outcome_id')->nullable()->after('sasaran_id');
                $table->unsignedBigInteger('kegiatan_id')->nullable()->after('outcome_id');
                $table->unsignedBigInteger('sub_kegiatan_id')->nullable()->after('kegiatan_id');
            }

            // --- PERBAIKAN DI SINI ---
            // Kita beri nama index manual 'idx_pohon_rel' supaya tidak kepanjangan & error
            $table->index([
                'tujuan_id', 
                'sasaran_id', 
                'outcome_id', 
                'kegiatan_id', 
                'sub_kegiatan_id'
            ], 'idx_pohon_rel'); 
        });
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