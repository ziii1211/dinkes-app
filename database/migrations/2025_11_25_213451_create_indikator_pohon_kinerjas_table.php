<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_pohon_kinerjas', function (Blueprint $table) {
            $table->id();
            // Relasi ke Pohon Kinerja (Parent)
            $table->foreignId('pohon_kinerja_id')->constrained('pohon_kinerjas')->onDelete('cascade');
            
            // Nama Indikator
            $table->text('nama_indikator');
            
            // Target (Nilai) & Satuan -> SAYA BUKA KOMENTARNYA AGAR AKTIF
            $table->string('target')->nullable(); // Untuk Nilai
            $table->string('satuan')->nullable(); // Untuk Satuan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_pohon_kinerjas');
    }
};