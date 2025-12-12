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
        Schema::table('users', function (Blueprint $table) {
            // Cek dan tambah kolom username jika belum ada
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }

            // Cek dan tambah kolom role (Wajib untuk login logic baru)
            if (!Schema::hasColumn('users', 'role')) {
                // Default kita set 'pegawai' agar user lama punya role
                $table->string('role')->default('pegawai')->after('password'); 
            }

            // Cek dan tambah kolom nip
            if (!Schema::hasColumn('users', 'nip')) {
                $table->string('nip')->nullable()->after('role');
            }

            // Cek dan tambah kolom jabatan
            if (!Schema::hasColumn('users', 'jabatan')) {
                $table->string('jabatan')->nullable()->after('nip');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $columns = ['username', 'role', 'nip', 'jabatan'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};