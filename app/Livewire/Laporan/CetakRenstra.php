<?php

namespace App\Livewire\Laporan;

use Livewire\Component;
use App\Models\Tujuan; // Mengambil model Tujuan sesuai database kamu

class CetakRenstra extends Component
{
    public $tahun; // Untuk menampung filter tahun
    public $isPreview = false; // Status apakah tombol sudah diklik

    // Fungsi ini dipanggil saat tombol "Tampilkan Preview" diklik
    public function tampilkanData()
    {
        $this->isPreview = true;
    }

    public function render()
    {
        $dataTujuan = [];

        // Jika tombol tampilkan diklik, tarik data dari database
        if ($this->isPreview) {
            // Kita ambil data Tujuan dan indikatornya untuk sekadar preview di layar
            $dataTujuan = Tujuan::with('pohonKinerja.indikators')->get();
        }

        return view('livewire.pusat-laporan.cetak-renstra', [
            'dataTujuan' => $dataTujuan
        ]);
    }
}