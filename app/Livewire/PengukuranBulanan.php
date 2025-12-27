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
        $user = Auth::user();

        // 1. Query Dasar
        $query = Jabatan::query()->with('pegawai');

        // 2. LOGIKA HAK AKSES (Updated)
        // Jika user BUKAN Admin DAN BUKAN Pimpinan, baru kita batasi.
        // Artinya: Admin & Pimpinan bebas melihat semua.
        if ($user->role !== 'admin' && $user->role !== 'pimpinan') {
            
            // Cek data pegawai user tersebut
            if ($user->pegawai && $user->pegawai->jabatan_id) {
                // Hanya tampilkan jabatannya sendiri
                $query->where('id', $user->pegawai->jabatan_id);
            } else {
                // Blokir jika tidak jelas
                $query->where('id', 0);
            }
        }

        // 3. Filter Pencarian
        $query->when($this->search, function($q) {
            $q->where('nama', 'like', '%'.$this->search.'%')
              ->orWhereHas('pegawai', function($sq) {
                  $sq->where('nama', 'like', '%'.$this->search.'%')
                     ->orWhere('nip', 'like', '%'.$this->search.'%');
              });
        });

        $jabatans = $query->paginate($this->perPage);

        return view('livewire.pengukuran-bulanan', [
            'jabatans' => $jabatans
        ]);
    }
}