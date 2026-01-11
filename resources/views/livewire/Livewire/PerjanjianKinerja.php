<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Auth;

class PerjanjianKinerja extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function render()
    {
        $user = Auth::user();
        $query = Jabatan::query();

        // --- UPDATE: MEMBUKA AKSES TAMPILAN UNTUK PEGAWAI ---
        // Sebelumnya ada logika pembatasan di sini.
        // Sekarang kita biarkan Pegawai melihat semua data (sama seperti Admin),
        // sesuai permintaan "samakan tampilan".
        
        /* // LOGIKA LAMA (YANG DIHAPUS/KOMENTAR):
        if ($user->role !== 'admin' && $user->role !== 'pimpinan') {
            if ($user->pegawai && $user->pegawai->jabatan_id) {
                $query->where('id', $user->pegawai->jabatan_id);
            } else {
                $query->where('id', 0);
            }
        }
        */

        // Filter Pencarian Tetap Ada
        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        $jabatans = $query->orderBy('id', 'asc')
                          ->paginate($this->perPage);

        return view('livewire.perjanjian-kinerja', [
            'jabatans' => $jabatans
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
}