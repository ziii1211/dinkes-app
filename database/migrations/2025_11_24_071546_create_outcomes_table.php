<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outcomes', function (Blueprint $table) {
            $table->id();
            // Relasi ke Sasaran Renstra (Induk)
            $table->foreignId('sasaran_id')->constrained('sasarans')->onDelete('cascade');
            
            $table->text('outcome'); // Deskripsi Outcome
            
            // TAMBAHAN: Relasi Penanggung Jawab
            $table->unsignedBigInteger('jabatan_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outcomes');
    }
};