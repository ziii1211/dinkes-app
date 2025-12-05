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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $jabatans = Jabatan::query()
            ->with('pegawai')
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