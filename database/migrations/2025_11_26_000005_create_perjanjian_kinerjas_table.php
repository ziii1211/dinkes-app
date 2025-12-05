<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perjanjian_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jabatan_id')->constrained('jabatans')->onDelete('cascade');
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->onDelete('set null');
            
            $table->year('tahun');
            $table->string('keterangan'); // Contoh: "PK Kepala Dinas Tahun 2025"
            $table->string('status')->default('draft'); // draft, final
            $table->date('tanggal_penetapan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perjanjian_kinerjas');
    }
};