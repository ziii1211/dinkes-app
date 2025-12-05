<?php

namespace App\Livewire\Pimpinan;

use Livewire\Component;
use App\Models\Pegawai;
use App\Models\PerjanjianKinerja;

class Dashboard extends Component
{
    public function render()
    {
        // Data Ringkas untuk Pimpinan
        $totalPegawai = Pegawai::count();
        $pkMenunggu = PerjanjianKinerja::where('status_verifikasi', 'pending')->count();
        $pkDisetujui = PerjanjianKinerja::where('status_verifikasi', 'disetujui')->count();

        return view('livewire.pimpinan.dashboard', [
            'totalPegawai' => $totalPegawai,
            'pkMenunggu' => $pkMenunggu,
            'pkDisetujui' => $pkDisetujui
        ]);
    }
}