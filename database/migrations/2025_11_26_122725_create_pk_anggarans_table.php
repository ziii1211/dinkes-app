<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pk_anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjanjian_kinerja_id')->constrained('perjanjian_kinerjas')->onDelete('cascade');
            
            // Kita siapkan kolom untuk Program/Kegiatan/Sub Kegiatan
            // Nanti alurnya menyesuaikan instruksi Anda (apakah teks manual atau relasi ID)
            // Untuk sementara saya buat fleksibel (bisa relasi ke SubKegiatan atau teks)
            $table->foreignId('sub_kegiatan_id')->nullable()->constrained('sub_kegiatans')->onDelete('set null');
            $table->text('nama_program_kegiatan')->nullable(); // Jika manual
            
            $table->decimal('anggaran', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pk_anggarans');
    }
};