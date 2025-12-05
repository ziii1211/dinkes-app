<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip');
            $table->string('status')->default('Definitif'); // Definitif / Plt / dll
            $table->string('foto')->nullable(); // Untuk menyimpan nama file foto
            
            // Relasi ke Tabel Jabatan (Agar nama jabatan muncul di bawah nama pegawai)
            $table->unsignedBigInteger('jabatan_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};