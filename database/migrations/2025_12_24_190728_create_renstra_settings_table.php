<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renstra_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_dokumen')->nullable()->default('031');
            $table->string('tanggal_dokumen')->nullable()->default('12 September 2025');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renstra_settings');
    }
};