<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_konsolidasi_anggarans', function (Blueprint $table) {
            $table->id();
            // Terhubung ke Laporan Utama
            $table->foreignId('laporan_konsolidasi_id')
                  ->constrained('laporan_konsolidasis')
                  ->onDelete('cascade');
            
            // Opsional: Terhubung ke Program ATAU Kegiatan
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
            $table->foreignId('kegiatan_id')->nullable()->constrained('kegiatans')->onDelete('cascade');
            
            // Nilai Anggaran & Realisasi
            $table->decimal('pagu_anggaran', 15, 2)->default(0);
            $table->decimal('pagu_realisasi', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_konsolidasi_anggarans');
    }
};