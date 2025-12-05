<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crosscutting_kinerjas', function (Blueprint $table) {
            $table->id();
            
            // 1. Kinerja Sumber (Dari tabel pohon_kinerjas)
            $table->foreignId('pohon_sumber_id')->constrained('pohon_kinerjas')->onDelete('cascade');
            
            // 2. SKPD Tujuan (Dari tabel skpd_tujuans)
            $table->foreignId('skpd_tujuan_id')->constrained('skpd_tujuans')->onDelete('cascade');
            
            // 3. Kinerja Tujuan (Dari tabel pohon_kinerjas - Sesuai instruksi alurnya sama no 1)
            // Note: Idealnya ini ke tabel pohon milik SKPD lain, tapi karena single app, kita relasikan ke tabel yang sama
            $table->foreignId('pohon_tujuan_id')->constrained('pohon_kinerjas')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crosscutting_kinerjas');
    }
};