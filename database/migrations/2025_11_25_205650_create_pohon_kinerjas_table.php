<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pohon_kinerjas', function (Blueprint $table) {
            $table->id();
            // Relasi ke Sasaran RPJMD (Tabel Tujuans)
            $table->foreignId('tujuan_id')->constrained('tujuans')->onDelete('cascade');
            
            // Kondisi yang diharapkan (Nama Pohon)
            $table->text('nama_pohon');
            
            // Jenis Pohon (Root/Level 1/Level 2, dll - Opsional untuk pengembangan nanti)
            // $table->string('jenis')->default('sasaran_strategis'); 
            
            // Relasi Hierarki (Parent ID) - Untuk struktur pohon bertingkat
            $table->unsignedBigInteger('parent_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pohon_kinerjas');
    }
};