<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class FixSekretarisSeeder extends Seeder
{
    public function run()
    {
        // 1. Cari Jabatan yang mengandung kata "Sekretaris"
        // (Biasanya 'Sekretaris Dinas' atau 'Sekretaris')
        $jabatan = Jabatan::where('nama', 'LIKE', '%Sekretaris%')->first();

        if (!$jabatan) {
            $this->command->error("Gagal: Data Jabatan 'Sekretaris' tidak ditemukan di Master Data.");
            return;
        }

        $this->command->info("Ditemukan Jabatan: " . $jabatan->nama);

        // 2. Cari Pegawai yang menduduki jabatan tersebut
        $pegawai = Pegawai::where('jabatan_id', $jabatan->id)->first();

        if (!$pegawai) {
            $this->command->error("Gagal: Belum ada Pegawai yang diinput untuk jabatan ini.");
            $this->command->warn("Silakan input data pegawai untuk Sekretaris di menu Struktur Organisasi terlebih dahulu.");
            return;
        }

        $this->command->info("Ditemukan Pegawai: " . $pegawai->nama . " | NIP: " . $pegawai->nip);

        // 3. UPDATE ATAU BUAT USER BARU
        // Kuncinya: Username HARUS SAMA PERSIS dengan $jabatan->nama
        User::updateOrCreate(
            ['nip' => $pegawai->nip], // Cari user berdasarkan NIP pegawai
            [
                'name'     => $pegawai->nama,
                'email'    => $pegawai->nip . '@dinkes.local', // Email dummy
                
                // INI YANG PALING PENTING:
                // Username diambil langsung dari tabel Jabatan agar cocok dengan Dropdown
                'username' => $jabatan->nama, 
                
                // Password diambil dari NIP
                'password' => Hash::make($pegawai->nip),
                
                'role'     => 'pegawai', // Atau 'admin' jika sekretaris butuh akses admin
                'jabatan'  => $jabatan->nama
            ]
        );

        $this->command->info("-------------------------------------------------------");
        $this->command->info("SUKSES MEMPERBAIKI AKUN SEKRETARIS");
        $this->command->info("-------------------------------------------------------");
        $this->command->info("Silakan Login dengan data berikut:");
        $this->command->info("Pilih Jabatan (Username) : " . $jabatan->nama);
        $this->command->info("Password (NIP)           : " . $pegawai->nip);
        $this->command->info("-------------------------------------------------------");
    }
}