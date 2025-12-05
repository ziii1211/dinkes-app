<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role dengan default 'admin'
            // Opsinya nanti bisa: 'admin', 'pimpinan', 'operator'
            $table->string('role')->default('admin')->after('email');
            
            // Opsional: Menambahkan NIP atau Jabatan jika perlu
            $table->string('nip')->nullable()->after('role');
            $table->string('jabatan')->nullable()->after('nip');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'nip', 'jabatan']);
        });
    }
};