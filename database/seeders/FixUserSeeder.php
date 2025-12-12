<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixUserSeeder extends Seeder
{
    public function run()
    {
        // 1. UPDATE/BUAT AKUN PEGAWAI
        User::updateOrCreate(
            ['email' => 'pegawai@dinkes.com'],
            [
                'name' => 'Staf Pegawai',
                'username' => 'pegawai', // Username baru
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'nip' => '199001012022031001'
            ]
        );

        // 2. UPDATE/BUAT AKUN ADMIN
        User::updateOrCreate(
            ['email' => 'admin@dinkes.com'],
            [
                'name' => 'Administrator Perencanaan',
                'username' => 'admin', // Username baru
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nip' => '-'
            ]
        );

        // 3. BUAT AKUN PIMPINAN
        User::updateOrCreate(
            ['email' => 'pimpinan@dinkes.com'],
            [
                'name' => 'Kepala Dinas Kesehatan',
                'username' => 'pimpinan', // Username baru
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
                'nip' => '197709232006041015'
            ]
        );

        $this->command->info('Sukses: 3 Akun dengan Username (pegawai, admin, pimpinan) siap digunakan!');
    }
}