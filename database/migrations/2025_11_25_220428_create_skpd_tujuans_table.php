<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skpd_tujuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_skpd');
            $table->timestamps();
        });
        
        // SEEDER OTOMATIS (Agar dropdown tidak kosong saat pertama kali dijalankan)
        DB::table('skpd_tujuans')->insert([
            ['nama_skpd' => 'DINAS KELAUTAN DAN PERIKANAN'],
            ['nama_skpd' => 'DINAS PERKEBUNAN DAN PETERNAKAN'],
            ['nama_skpd' => 'DINAS PEMBERDAYAAN MASYARAKAT DAN DESA'],
            ['nama_skpd' => 'DINAS PERTANIAN DAN KETAHANAN PANGAN'],
            ['nama_skpd' => 'DINAS KESEHATAN'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('skpd_tujuans');
    }
};