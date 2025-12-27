<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SyncUserPegawaiSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Memulai sinkronisasi User dari data Pegawai...');

        // 1. Ambil semua pegawai yang punya jabatan
        // Kita prioritaskan yang 'Definitif' agar jika ada 2 orang di jabatan sama, 
        // yang Definitif yang 'memegang' akun tersebut.
        $pegawais = Pegawai::whereNotNull('jabatan_id')
                           ->with('jabatan')
                           ->orderByRaw("FIELD(status, 'Definitif', 'Plt', 'Plh')") 
                           ->get();

        $count = 0;

        foreach ($pegawais as $pegawai) {
            // Skip jika jabatan tidak ada namanya (data kotor)
            if (!$pegawai->jabatan || empty($pegawai->jabatan->nama)) {
                continue;
            }

            // Username = Nama Jabatan
            $username = $pegawai->jabatan->nama;
            
            // Password = NIP
            $password = $pegawai->nip;

            // Buat atau Update User berdasarkan NIP
            // Jadi kalau user sudah ada, datanya cuma diupdate
            try {
                User::updateOrCreate(
                    ['nip' => $pegawai->nip], // Kunci pencarian (NIP harus unik)
                    [
                        'name'     => $pegawai->nama,
                        'email'    => $pegawai->nip . '@dinkes.local', // Email dummy saja karena login pakai username
                        'username' => $username, // INI YANG AKAN DIPAKAI LOGIN
                        'password' => Hash::make($password), // Password dari NIP
                        'role'     => 'pegawai', // Default role
                        'jabatan'  => $username,
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $this->command->error("Gagal buat user untuk: " . $pegawai->nama . " - Error: " . $e->getMessage());
            }
        }

        $this->command->info("Sukses! $count User berhasil dibuat/diupdate dari data Pegawai.");
        $this->command->info("Username = Nama Jabatan (Persis dengan data input)");
        $this->command->info("Password = NIP");
    }
}