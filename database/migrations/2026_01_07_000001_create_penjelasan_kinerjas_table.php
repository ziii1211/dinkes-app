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
        // Hapus tabel lama jika sudah pernah dibuat sebelumnya
        // agar struktur bisa diperbarui total menjadi 3 kolom
        Schema::dropIfExists('penjelasan_kinerjas');

        Schema::create('penjelasan_kinerjas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jabatan_id'); // Relasi ke Jabatan
            $table->integer('bulan');
            $table->year('tahun');
            
            // Tiga kolom utama (Input Manual)
            $table->text('upaya')->nullable();
            $table->text('hambatan')->nullable();
            $table->text('tindak_lanjut')->nullable();
            
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['jabatan_id', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjelasan_kinerjas');
    }
};