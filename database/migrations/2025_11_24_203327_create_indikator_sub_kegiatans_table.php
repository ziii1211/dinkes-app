<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_sub_kegiatans', function (Blueprint $table) {
            $table->id();
            // Relasi ke Sub Kegiatan
            $table->foreignId('sub_kegiatan_id')->constrained('sub_kegiatans')->onDelete('cascade');
            
            $table->text('keterangan');
            $table->string('satuan');
            
            // Target & Pagu Anggaran
            $table->string('target_2025')->nullable();
            $table->decimal('pagu_2025', 15, 2)->default(0); 
            // ... (lanjutkan untuk tahun 2026-2030) ...
            $table->string('target_2026')->nullable();
            $table->decimal('pagu_2026', 15, 2)->default(0);
            $table->string('target_2027')->nullable();
            $table->decimal('pagu_2027', 15, 2)->default(0);
            $table->string('target_2028')->nullable();
            $table->decimal('pagu_2028', 15, 2)->default(0);
            $table->string('target_2029')->nullable();
            $table->decimal('pagu_2029', 15, 2)->default(0);
            $table->string('target_2030')->nullable();
            $table->decimal('pagu_2030', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_sub_kegiatans');
    }
};