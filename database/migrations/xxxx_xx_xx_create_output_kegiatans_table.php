<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel baru
        Schema::create('output_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatans')->onDelete('cascade');
            $table->text('deskripsi'); // Pengganti kolom output
            $table->timestamps();
        });

        // 2. Migrasikan data lama (Penting agar data tidak hilang)
        $kegiatans = DB::table('kegiatans')->whereNotNull('output')->get();
        foreach ($kegiatans as $k) {
            if (!empty($k->output)) {
                DB::table('output_kegiatans')->insert([
                    'kegiatan_id' => $k->id,
                    'deskripsi' => $k->output,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 3. Hapus kolom output lama di tabel kegiatans
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->dropColumn('output');
        });
    }

    public function down(): void
    {
        // Kembalikan kolom jika rollback (opsional, disederhanakan)
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->text('output')->nullable();
        });
        Schema::dropIfExists('output_kegiatans');
    }
};