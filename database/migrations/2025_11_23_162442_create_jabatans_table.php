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
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama Jabatan (misal: Kepala Dinas)
            
            // Menyimpan ID atasan (Bisa kosong/nullable jika dia paling atas)
            $table->unsignedBigInteger('parent_id')->nullable();
            
            // Menyimpan level urutan (0 = Atasan, 1 = Anak, 2 = Cucu, dst)
            $table->integer('level')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};