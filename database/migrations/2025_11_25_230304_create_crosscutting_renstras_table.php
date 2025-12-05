<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crosscutting_renstras', function (Blueprint $table) {
            $table->id();
            
            // Kinerja Sumber (Polymorphic: Sasaran/Outcome/Kegiatan)
            $table->string('sumber_type');
            $table->unsignedBigInteger('sumber_id');
            
            // SKPD Tujuan
            $table->foreignId('skpd_tujuan_id')->constrained('skpd_tujuans')->onDelete('cascade');
            
            // Kinerja Tujuan (Polymorphic: Sasaran/Outcome/Kegiatan)
            $table->string('tujuan_type');
            $table->unsignedBigInteger('tujuan_id');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crosscutting_renstras');
    }
};