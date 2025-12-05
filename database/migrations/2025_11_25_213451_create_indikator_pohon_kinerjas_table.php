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
            
            // Target & Satuan (Opsional jika ingin ditambahkan nanti)
            // $table->string('target')->nullable();
            // $table->string('satuan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_pohon_kinerjas');
    }
};