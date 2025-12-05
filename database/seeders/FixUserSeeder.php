<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixUserSeeder extends Seeder
{
    public function run()
    {
        // 1. UPDATE/BUAT AKUN PEGAWAI (Penginput)
        User::updateOrCreate(
            ['email' => 'pegawai@dinkes.com'],
            [
                'name' => 'Staf Pegawai',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'nip' => '199001012022031001'
            ]
        );

        // 2. UPDATE/BUAT AKUN ADMIN (Pemantau)
        User::updateOrCreate(
            ['email' => 'admin@dinkes.com'],
            [
                'name' => 'Administrator Perencanaan',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'nip' => '-'
            ]
        );

        // 3. BUAT AKUN PIMPINAN (Verifikator) - BARU
        User::updateOrCreate(
            ['email' => 'pimpinan@dinkes.com'],
            [
                'name' => 'Kepala Dinas Kesehatan',
                'password' => Hash::make('password'),
                'role' => 'pimpinan', // Role baru
                'nip' => '197709232006041015'
            ]
        );

        $this->command->info('Sukses: 3 Akun (Pegawai, Admin, Pimpinan) siap digunakan!');
    }
}