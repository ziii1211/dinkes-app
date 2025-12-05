<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengukuran_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->string('bulan'); // Januari, Februari, dst
            $table->decimal('capaian_kinerja', 5, 2)->default(0); // Persen
            $table->decimal('realisasi_anggaran', 15, 2)->default(0); // Rupiah
            $table->string('status')->default('draft'); // draft, final
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengukuran_kinerjas');
    }
};