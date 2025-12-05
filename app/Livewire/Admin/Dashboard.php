<?php

namespace App\Livewire\Admin;

use Livewire\Component;
// Import Model Indikator (Sesuaikan jika nama model berbeda di projectmu)
use App\Models\IndikatorTujuan;
use App\Models\IndikatorSasaran;
use App\Models\IndikatorOutcome; // Biasanya outcome program
use App\Models\IndikatorKegiatan;
use App\Models\IndikatorSubKegiatan;

class Dashboard extends Component
{
    public function render()
    {
        // Mengambil jumlah data real dari database
        // Gunakan try-catch agar jika tabel belum ada isinya, tidak error fatal (tetap 0)
        try {
            $data = [
                'ind_tujuan' => IndikatorTujuan::count(),
                'ind_sasaran' => IndikatorSasaran::count(),
                'ind_program' => IndikatorOutcome::count(), // Program biasanya punya Outcome
                'ind_kegiatan' => IndikatorKegiatan::count(),
                'ind_sub_kegiatan' => IndikatorSubKegiatan::count(),
            ];
        } catch (\Exception $e) {
            // Fallback jika migrasi belum lengkap
            $data = [
                'ind_tujuan' => 0, 'ind_sasaran' => 0, 'ind_program' => 0, 
                'ind_kegiatan' => 0, 'ind_sub_kegiatan' => 0
            ];
        }

        return view('livewire.admin.dashboard', $data);
    }
}