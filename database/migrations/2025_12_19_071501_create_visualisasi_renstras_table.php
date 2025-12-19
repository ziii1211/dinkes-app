<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('visualisasi_renstras', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('parent_id')->nullable(); // Untuk hirarki pohon
        $table->string('jabatan')->nullable(); // Judul Card
        
        // Kita simpan items (kinerja & indikator) dalam bentuk JSON
        // Biar tidak ribet bikin tabel relasi anak-anaknya lagi
        $table->longText('content_data')->nullable(); 
        
        $table->boolean('is_locked')->default(false);
        $table->timestamps();

        // Self Reference Relation
        $table->foreign('parent_id')->references('id')->on('visualisasi_renstras')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visualisasi_renstras');
    }
};
