<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_sasarans', function (Blueprint $table) {
            $table->id();
            // Relasi ke Sasaran
            $table->foreignId('sasaran_id')->constrained('sasarans')->onDelete('cascade');
            
            $table->text('keterangan');
            $table->string('satuan');
            $table->string('arah');
            
            // Target
            $table->string('target_2025')->nullable();
            $table->string('target_2026')->nullable();
            $table->string('target_2027')->nullable();
            $table->string('target_2028')->nullable();
            $table->string('target_2029')->nullable();
            $table->string('target_2030')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_sasarans');
    }
};