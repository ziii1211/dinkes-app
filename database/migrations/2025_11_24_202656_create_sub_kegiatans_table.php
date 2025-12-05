<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_kegiatans', function (Blueprint $table) {
            $table->id();
            // Relasi ke Kegiatan (Induk)
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');
            
            $table->string('kode'); // Contoh: 1.02.02.1.01.01
            $table->text('nama');   // Nama Sub Kegiatan
            $table->text('output')->nullable(); // Deskripsi Output
            
            // Penanggung Jawab Sub Kegiatan
            $table->unsignedBigInteger('jabatan_id')->nullable(); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_kegiatans');
    }
};