<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Auth;

class PengukuranBulanan extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $tahun;

    public function mount()
    {
        $this->tahun = date('Y');
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedTahun() { $this->resetPage(); }

    public function render()
    {
        // QUERY DIBUKA UNTUK SEMUA ROLE (Admin, Pimpinan, Pegawai)
        // Tidak ada lagi logika "if user->role !== admin" untuk membatasi query.
        
        $query = Jabatan::query()->with('pegawai');

        // Filter Pencarian
        $query->when($this->search, function($q) {
                $q->where('nama', 'like', '%'.$this->search.'%')
                  ->orWhereHas('pegawai', function($sq) {
                      $sq->where('nama', 'like', '%'.$this->search.'%')
                         ->orWhere('nip', 'like', '%'.$this->search.'%');
                  });
            });

        $jabatans = $query->orderBy('id', 'asc')->paginate($this->perPage);

        return view('livewire.pengukuran-bulanan', [
            'jabatans' => $jabatans
        ]);
    }
}