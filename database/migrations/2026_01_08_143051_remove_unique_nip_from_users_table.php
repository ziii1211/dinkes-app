<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus index unique pada nip jika ada
            // Kita gunakan try-catch atau cek dulu agar tidak error jika index tidak ada
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('users');

            if (array_key_exists('users_nip_unique', $indexes)) {
                $table->dropUnique('users_nip_unique');
            }
        });
    }

    public function down(): void
    {
        // Kembalikan jika perlu (opsional)
    }
};