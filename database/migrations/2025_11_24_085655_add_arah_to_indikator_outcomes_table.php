<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indikator_outcomes', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada biar tidak error double
            if (!Schema::hasColumn('indikator_outcomes', 'arah')) {
                $table->string('arah')->nullable()->after('satuan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('indikator_outcomes', function (Blueprint $table) {
            $table->dropColumn('arah');
        });
    }
};