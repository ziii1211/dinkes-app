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
    Schema::table('indikator_kegiatans', function (Blueprint $table) {
        // Menambahkan kolom arah setelah kolom satuan
        $table->string('arah')->nullable()->after('satuan');
    });
}

public function down(): void
{
    Schema::table('indikator_kegiatans', function (Blueprint $table) {
        $table->dropColumn('arah');
    });
}
};
