<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_outcomes', function (Blueprint $table) {
            $table->id();
            // Relasi ke Outcome (Induk)
            $table->foreignId('outcome_id')->constrained('outcomes')->onDelete('cascade');
            
            $table->text('keterangan'); // Nama Indikator
            $table->string('satuan');
            
            // Target Periode 2025-2030
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
        Schema::dropIfExists('indikator_outcomes');
    }
};