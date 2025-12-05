<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('realisasi_kinerjas', function (Blueprint $table) {
            $table->id();
            // ID Indikator (Relasi ke tabel indikator_pohon_kinerjas/pk_indikators)
            // Sesuaikan tipe datanya dengan id di tabel indikator Anda (biasanya bigInteger)
            $table->unsignedBigInteger('indikator_id'); 
            
            $table->integer('bulan'); // 1 - 12
            $table->year('tahun');    // 2025, 2026
            
            // Kolom Data
            $table->decimal('realisasi', 15, 2)->nullable(); // Angka realisasi
            $table->decimal('capaian', 5, 2)->nullable();    // Persentase capaian
            $table->text('catatan')->nullable();             // Catatan/Analisis
            
            $table->timestamps();

            // Opsional: Index agar pencarian cepat
            $table->index(['indikator_id', 'bulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('realisasi_kinerjas');
    }
};