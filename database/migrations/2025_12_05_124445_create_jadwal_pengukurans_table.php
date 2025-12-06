<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('jadwal_pengukurans', function (Blueprint $table) {
        $table->id();
        $table->year('tahun');
        $table->tinyInteger('bulan'); // 1 - 12
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai'); // Ini batas tenggat waktunya
        $table->boolean('is_active')->default(true); // Status manual buka/tutup
        $table->timestamps();
        
        // Mencegah duplikasi jadwal untuk bulan/tahun yang sama
        $table->unique(['tahun', 'bulan']);
    });
}

public function down(): void
{
    Schema::dropIfExists('jadwal_pengukurans');
}
};
