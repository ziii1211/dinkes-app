<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sasarans', function (Blueprint $table) {
            $table->id();
            // Relasi ke Tujuan (Induknya)
            $table->foreignId('tujuan_id')->constrained('tujuans')->onDelete('cascade');
            $table->text('sasaran');
            // Penanggung Jawab
            $table->unsignedBigInteger('jabatan_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sasarans');
    }
};