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
        // 1. TABEL PEGAWAI
        // Optimasi: Search bar (Nama & NIP)
        Schema::table('pegawais', function (Blueprint $table) {
            // Index agar pencarian "WHERE nama LIKE..." jauh lebih cepat
            $table->index('nama'); 
            // Index agar pencarian NIP instan
            $table->index('nip');
            // Pastikan foreign key jabatan juga ter-index (biasanya otomatis, tapi kita pastikan)
            $table->index('jabatan_id');
        });

        // 2. TABEL JABATAN
        // Optimasi: Struktur Pohon / Hirarki (StrukturOrganisasi.php)
        Schema::table('jabatans', function (Blueprint $table) {
            // Sangat penting untuk fungsi sortJabatanTree (Parent-Child relationship)
            $table->index('parent_id');
        });

        // 3. TABEL PERJANJIAN KINERJA (PK)
        // Optimasi: Filter Tahun saat load data dashboard
        Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
            $table->index('tahun');
            $table->index('jabatan_id');
        });

        // 4. TABEL REALISASI KINERJA
        // Optimasi: Pengukuran Kinerja Bulanan
        Schema::table('realisasi_kinerjas', function (Blueprint $table) {
            // Composite Index: Karena kita sering query "WHERE tahun = ? AND bulan = ?" secara bersamaan
            $table->index(['tahun', 'bulan']);
            $table->index('indikator_id');
        });

        // 5. TABEL RENCANA AKSI & REALISASINYA
        Schema::table('rencana_aksis', function (Blueprint $table) {
            $table->index('tahun');
            $table->index('jabatan_id');
        });

        Schema::table('realisasi_rencana_aksis', function (Blueprint $table) {
            $table->index(['tahun', 'bulan']);
            $table->index('rencana_aksi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus index jika migration di-rollback (agar bersih kembali)
        
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropIndex(['nama']);
            $table->dropIndex(['nip']);
            $table->dropIndex(['jabatan_id']);
        });

        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
        });

        Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
            $table->dropIndex(['tahun']);
            $table->dropIndex(['jabatan_id']);
        });

        Schema::table('realisasi_kinerjas', function (Blueprint $table) {
            $table->dropIndex(['tahun', 'bulan']);
            $table->dropIndex(['indikator_id']);
        });

        Schema::table('rencana_aksis', function (Blueprint $table) {
            $table->dropIndex(['tahun']);
            $table->dropIndex(['jabatan_id']);
        });

        Schema::table('realisasi_rencana_aksis', function (Blueprint $table) {
            $table->dropIndex(['tahun', 'bulan']);
            $table->dropIndex(['rencana_aksi_id']);
        });
    }
};