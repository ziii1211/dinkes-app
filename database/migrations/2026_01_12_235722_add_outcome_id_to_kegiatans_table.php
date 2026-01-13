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
        Schema::table('kegiatans', function (Blueprint $table) {
            // Menambahkan kolom outcome_id setelah program_id
            // nullable() artinya boleh kosong dulu (untuk data lama)
            // constrained() agar terhubung ke tabel outcomes
            // onDelete('cascade') agar jika outcome dihapus, kegiatan ikut terhapus
            $table->foreignId('outcome_id')
                  ->nullable()
                  ->after('program_id')
                  ->constrained('outcomes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            // Hapus foreign key dulu baru kolomnya
            $table->dropForeign(['outcome_id']);
            $table->dropColumn('outcome_id');
        });
    }
};