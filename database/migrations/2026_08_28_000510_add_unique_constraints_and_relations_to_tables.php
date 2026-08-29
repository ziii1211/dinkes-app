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
        // 1. Tujuans
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE tujuans ADD UNIQUE INDEX tujuans_unique_sasaran_tujuan (sasaran_rpjmd, tujuan(255))');

        // 2. Sasarans
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE sasarans ADD UNIQUE INDEX sasarans_unique_tujuan_sasaran (tujuan_id, sasaran(255))');

        // 3. Outcomes
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE outcomes ADD UNIQUE INDEX outcomes_unique_sasaran_outcome (sasaran_id, outcome(255))');

        // 4. Programs
        Schema::table('programs', function (Blueprint $table) {
            $table->unique('kode', 'programs_unique_kode');
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE programs ADD UNIQUE INDEX programs_unique_nama (nama(255))');

        // 5. Kegiatans
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->unique(['program_id', 'kode'], 'kegiatans_unique_program_kode');
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE kegiatans ADD UNIQUE INDEX kegiatans_unique_program_nama (program_id, nama(255))');

        // 6. Sub Kegiatans
        Schema::table('sub_kegiatans', function (Blueprint $table) {
            $table->unique(['kegiatan_id', 'kode'], 'subkegiatans_unique_keg_kode');
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE sub_kegiatans ADD UNIQUE INDEX subkegiatans_unique_keg_nama (kegiatan_id, nama(255))');

        // 7. Perjanjian Kinerjas
        Schema::table('perjanjian_kinerjas', function (Blueprint $table) {
            $table->unique(['jabatan_id', 'tahun', 'bulan'], 'pk_unique_jabatan_tahun_bulan');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tujuans', function (Blueprint $table) { $table->dropIndex('tujuans_unique_sasaran_tujuan'); });
        Schema::table('sasarans', function (Blueprint $table) { $table->dropIndex('sasarans_unique_tujuan_sasaran'); });
        Schema::table('outcomes', function (Blueprint $table) { $table->dropIndex('outcomes_unique_sasaran_outcome'); });
        
        Schema::table('programs', function (Blueprint $table) { 
            $table->dropUnique('programs_unique_kode'); 
            $table->dropIndex('programs_unique_nama'); 
        });
        
        Schema::table('kegiatans', function (Blueprint $table) { 
            $table->dropUnique('kegiatans_unique_program_kode'); 
            $table->dropIndex('kegiatans_unique_program_nama'); 
        });
        
        Schema::table('sub_kegiatans', function (Blueprint $table) { 
            $table->dropUnique('subkegiatans_unique_keg_kode'); 
            $table->dropIndex('subkegiatans_unique_keg_nama'); 
        });
        
        Schema::table('perjanjian_kinerjas', function (Blueprint $table) { 
            $table->dropUnique('pk_unique_jabatan_tahun_bulan'); 
        });
    }
};
