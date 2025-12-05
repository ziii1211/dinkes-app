<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('realisasi_rencana_aksis', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Rencana Aksi
            $table->unsignedBigInteger('rencana_aksi_id'); 
            
            $table->integer('bulan'); // 1 - 12
            $table->year('tahun');    // 2025
            
            // Data Realisasi
            $table->decimal('realisasi', 15, 2)->nullable(); // Angka Realisasi
            
            // Opsional: Jika butuh kolom capaian tersimpan (biasanya dihitung on-the-fly)
            // $table->decimal('capaian', 5, 2)->nullable();

            $table->timestamps();

            // Index agar pencarian cepat
            $table->index(['rencana_aksi_id', 'bulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('realisasi_rencana_aksis');
    }
};