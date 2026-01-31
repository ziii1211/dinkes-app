<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Header Laporan
        Schema::create('laporan_konsolidasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('bulan'); // Januari, Februari, dst
            $table->integer('tahun');
            $table->timestamps();
        });

        // Tabel Detail (Isi Excel)
        Schema::create('detail_laporan_konsolidasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_konsolidasi_id')->constrained()->cascadeOnDelete();
            
            // Menyimpan referensi ID dari data master (opsional, untuk relasi)
            $table->unsignedBigInteger('sub_kegiatan_id')->nullable(); 
            
            // Data yang disimpan statis (snapshot) agar jika master berubah, laporan lama tidak rusak
            $table->string('kode'); 
            $table->text('nama_program_kegiatan'); // Program/Kegiatan/SubKegiatan
            
            // Input Manual
            $table->string('sub_output')->nullable();
            $table->string('satuan_unit')->nullable(); // Contoh: "3 Dokumen"
            $table->decimal('pagu_anggaran', 15, 2)->default(0);
            $table->decimal('pagu_realisasi', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_laporan_konsolidasis');
        Schema::dropIfExists('laporan_konsolidasis');
    }
};