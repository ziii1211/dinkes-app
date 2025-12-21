<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jabatan;

class PengukuranBulanan extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $tahun; // 1. Tambahkan properti tahun

    // 2. Set default tahun saat halaman dibuka (misal tahun sekarang atau 2025)
    public function mount()
    {
        $this->tahun = date('Y');
        // Jika ingin default fix 2025, ganti baris atas dengan: $this->tahun = '2025';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Reset halaman jika tahun berubah (opsional, untuk UX yang lebih baik)
    public function updatedTahun()
    {
        $this->resetPage();
    }

    public function render()
    {
        $jabatans = Jabatan::query()
            ->with('pegawai')
            // Opsi Tambahan: Jika Anda ingin memfilter Jabatan yang HANYA punya perjanjian kinerja di tahun tsb,
            // Anda bisa uncomment baris di bawah ini (sesuaikan nama relasinya):
            // ->whereHas('perjanjianKinerja', function($q) {
            //     $q->where('tahun', $this->tahun);
            // })
            ->when($this->search, function($q) {
                $q->where('nama', 'like', '%'.$this->search.'%')
                  ->orWhereHas('pegawai', function($sq) {
                      $sq->where('nama', 'like', '%'.$this->search.'%')
                         ->orWhere('nip', 'like', '%'.$this->search.'%');
                  });
            })
            ->paginate($this->perPage);

        return view('livewire.pengukuran-bulanan', [
            'jabatans' => $jabatans
        ]);
    }
}