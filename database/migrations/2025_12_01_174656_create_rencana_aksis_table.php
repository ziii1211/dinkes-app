<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rencana_aksis', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Jabatan (Siapa pemilik Rencana Aksi ini)
            $table->unsignedBigInteger('jabatan_id');
            
            // Relasi ke PK (Opsional, jika Rencana Aksi terkait langsung dengan PK tertentu)
            // $table->unsignedBigInteger('perjanjian_kinerja_id')->nullable();

            $table->year('tahun');
            
            $table->text('nama_aksi'); // Uraian Rencana Aksi
            $table->string('target')->nullable(); // Target (Angka)
            $table->string('satuan')->nullable(); // Satuan (Dokumen, Kegiatan, dll)
            
            $table->timestamps();

            // Index
            $table->index(['jabatan_id', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rencana_aksis');
    }
};