<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    // Method ini jalan otomatis saat halaman dimuat
    public function mount()
    {
        // PENJAGA: Jika yang login adalah ADMIN, lempar ke Dashboard Admin
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}