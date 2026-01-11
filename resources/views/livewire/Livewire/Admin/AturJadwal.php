<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\JadwalPengukuran;
use Carbon\Carbon;

class AturJadwal extends Component
{
    public $tahun;
    public $bulan;
    public $tanggal_mulai;
    public $tanggal_selesai; // Tenggat waktu
    public $lama_hari = 7; // Default 7 hari

    public function mount()
    {
        $this->tahun = date('Y');
        $this->bulan = date('n');
        $this->tanggal_mulai = date('Y-m-d');
        // Default tenggat 7 hari dari sekarang
        $this->tanggal_selesai = Carbon::parse($this->tanggal_mulai)->addDays(7)->format('Y-m-d');
    }

    // Hitung otomatis tanggal selesai jika durasi hari diubah
    public function updatedLamaHari()
    {
        if ($this->tanggal_mulai && $this->lama_hari) {
            $this->tanggal_selesai = Carbon::parse($this->tanggal_mulai)->addDays($this->lama_hari)->format('Y-m-d');
        }
    }

    public function simpanJadwal()
    {
        $this->validate([
            'tahun' => 'required',
            'bulan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        JadwalPengukuran::updateOrCreate(
            ['tahun' => $this->tahun, 'bulan' => $this->bulan],
            [
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'is_active' => true
            ]
        );

        session()->flash('message', 'Jadwal pengisian berhasil disimpan.');
    }

    public function render()
    {
        $jadwals = JadwalPengukuran::where('tahun', $this->tahun)->orderBy('bulan')->get();
        return view('livewire.admin.atur-jadwal', compact('jadwals'));
    }
}