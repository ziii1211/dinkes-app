<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Kinerja Utama (Sasaran PK)
        Schema::create('pk_sasarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjanjian_kinerja_id')->constrained('perjanjian_kinerjas')->onDelete('cascade');
            $table->text('sasaran');
            $table->timestamps();
        });

        // Tabel Indikator Kinerja Utama (DIPERBARUI)
        Schema::create('pk_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pk_sasaran_id')->constrained('pk_sasarans')->onDelete('cascade');
            $table->text('nama_indikator');
            $table->string('satuan');
            
            // Target Per Tahun (Agar bisa diatur di modal Atur Target)
            $table->string('target_2025')->nullable();
            $table->string('target_2026')->nullable();
            $table->string('target_2027')->nullable();
            $table->string('target_2028')->nullable();
            $table->string('target_2029')->nullable();
            $table->string('target_2030')->nullable();
            
            $table->string('arah')->nullable(); // Naik/Turun
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pk_indikators');
        Schema::dropIfExists('pk_sasarans');
    }
};