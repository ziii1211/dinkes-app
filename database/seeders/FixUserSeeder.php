<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // 1. UPDATE/BUAT AKUN ADMIN
        // Username: admin
        // Password: admin123 (NIP Khusus Admin)
        User::updateOrCreate(
            ['email' => 'admin@dinkes.com'],
            [
                'name'     => 'Administrator Perencanaan',
                'username' => 'admin',     // Sesuai value <option value="admin">
                'password' => Hash::make('admin123'), 
                'role'     => 'admin',
                'nip'      => 'admin123',
                'jabatan'  => 'Administrator Sistem'
            ]
        );

        // 2. UPDATE/BUAT AKUN PIMPINAN (KEPALA DINAS)
        // Username: pimpinan
        // Password: Sesuai NIP (Contoh: 197709232006041015)
        User::updateOrCreate(
            ['email' => 'pimpinan@dinkes.com'],
            [
                'name'     => 'dr. DIAUDDIN, M.Kes', // Sesuaikan nama Kepala Dinas
                'username' => 'pimpinan',  // Sesuai value <option value="pimpinan">
                
                // Password disamakan dengan NIP
                'password' => Hash::make('197709232006041015'), 
                
                'role'     => 'pimpinan',  // Role khusus untuk redirect ke dashboard pimpinan
                'nip'      => '197709232006041015', // NIP Kepala Dinas
                'jabatan'  => 'Kepala Dinas Kesehatan'
            ]
        );


        $this->command->info('Sukses! Akun Pimpinan (Kadis) dan Admin telah diperbarui.');
        $this->command->info('Login Pimpinan -> Username: pimpinan | Pass: 197709232006041015');
    }
}