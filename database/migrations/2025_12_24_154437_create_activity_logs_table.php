<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            
            // 1. SIAPA (User ID & Nama saat kejadian)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // Simpan nama jaga-jaga kalau user dihapus
            
            // 2. APA (Action: CREATE, UPDATE, DELETE)
            $table->string('action'); 
            
            // 3. DIMANA (Model apa? Contoh: App\Models\PerjanjianKinerja)
            $table->string('model_type'); 
            $table->unsignedBigInteger('model_id'); // ID Data yang diubah
            
            // 4. DETAIL (Data Sebelum vs Sesudah)
            // Kita simpan dalam bentuk JSON agar fleksibel
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            
            // 5. INFO TAMBAHAN (IP Address & User Agent Browser)
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // 6. KAPAN (Created At)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};