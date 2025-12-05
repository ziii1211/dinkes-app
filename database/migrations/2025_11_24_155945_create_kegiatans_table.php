<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            // Relasi ke Program (Induk)
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            
            $table->string('kode'); // Contoh: 1.02.02.1.01
            $table->text('nama');   // Nama Kegiatan
            $table->text('output')->nullable(); // Deskripsi Output Kegiatan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};