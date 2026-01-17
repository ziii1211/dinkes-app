<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            // Menambahkan kolom output_kegiatan_id yang boleh kosong (nullable) dulu
            // agar data lama tidak error.
            $table->foreignId('output_kegiatan_id')
                  ->nullable()
                  ->after('kegiatan_id') // Posisi kolom setelah kegiatan_id
                  ->constrained('output_kegiatans')
                  ->onDelete('set null'); // Jika output dihapus, sub kegiatan jangan ikut hilang, tapi null kan saja
        });
    }

    public function down(): void
    {
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->dropForeign(['output_kegiatan_id']);
            $table->dropColumn('output_kegiatan_id');
        });
    }
};