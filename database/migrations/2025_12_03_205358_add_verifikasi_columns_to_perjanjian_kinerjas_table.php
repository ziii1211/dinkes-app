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
        Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
            
            // 1. Kolom Status Verifikasi
            // Default 'draft' (baru dibuat pegawai)
            $table->enum('status_verifikasi', ['draft', 'pending', 'disetujui', 'ditolak'])
                  ->default('draft')
                  ->after('tahun'); // Kita taruh setelah kolom tahun (atau kolom lain yg ada)

            // 2. Kolom Catatan Pimpinan (Jika ditolak/perlu revisi)
            $table->text('catatan_pimpinan')->nullable()->after('status_verifikasi');

            // 3. Kolom Tanggal Verifikasi
            $table->timestamp('tanggal_verifikasi')->nullable()->after('catatan_pimpinan');
            
            // 4. ID Pimpinan yang memverifikasi (Opsional, untuk rekam jejak)
            $table->unsignedBigInteger('verifikator_id')->nullable()->after('tanggal_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
            $table->dropColumn([
                'status_verifikasi', 
                'catatan_pimpinan', 
                'tanggal_verifikasi',
                'verifikator_id'
            ]);
        });
    }
};