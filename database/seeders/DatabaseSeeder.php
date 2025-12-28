<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Bersihkan Data User Lama (Reset)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Buat User Standar

        // Akun Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@dinkes.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'nip' => 'admin123',
            'jabatan' => 'Administrator Sistem'
        ]);

        // Akun Pimpinan
        User::create([
            'name' => 'Kepala Dinas',
            'username' => 'pimpinan',
            'email' => 'pimpinan@dinkes.com',
            'password' => Hash::make('123456'),
            'role' => 'pimpinan',
            'nip' => '197709232006041015',
            'jabatan' => 'Kepala Dinas Kesehatan'
        ]);

        // Akun Pegawai 
        User::create([
            'name' => 'Staf Pegawai',
            'username' => 'pegawai',
            'email' => 'pegawai@dinkes.com',
            'password' => Hash::make('123456'),
            'role' => 'pegawai',
            'nip' => '199001012022031001',
            'jabatan' => 'Staf Pelaksana'
        ]);
    }
}