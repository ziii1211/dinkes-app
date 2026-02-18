<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikator_sub_kegiatans', function (Blueprint $table) {
            // Menambahkan kolom target setelah kolom satuan
            $table->string('target')->nullable()->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('indikator_sub_kegiatans', function (Blueprint $table) {
            $table->dropColumn('target');
        });
    }
};