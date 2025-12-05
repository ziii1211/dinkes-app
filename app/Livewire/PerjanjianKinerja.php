<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jabatan; // PENTING: Kita ambil data dari Master Jabatan
use Illuminate\Support\Facades\Auth;

class PerjanjianKinerja extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function render()
    {
        // 1. QUERY KE MODEL JABATAN
        // Kita ambil semua jabatan yang ada di struktur organisasi
        $query = Jabatan::query();

        // 2. FILTER PENCARIAN
        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        // 3. URUTKAN DAN PAGINATION
        // Urutkan biar rapi (misal berdasarkan hierarki/parent_id atau id)
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