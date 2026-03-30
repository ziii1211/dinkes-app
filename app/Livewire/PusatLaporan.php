<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PerjanjianKinerja;
use App\Models\Jabatan;

class PusatLaporan extends Component
{
    // Modal State
    public $showPkModal = false;
    public $showAksiModal = false;
    public $showBulananModal = false;
    public $showTahunanModal = false;
    public $showEmonevModal = false;
    public $showPegawaiModal = false; // <-- Modal Pegawai
    
    public $pkList = [];
    public $bulanTerpilih = 1; 

    // State Data E-Monev & Pegawai
    public $listJabatan = [];
    public $emonevTahun;
    public $emonevJabatan = '';
    public $pegawaiJabatan = ''; // Untuk filter Pegawai

    public function openPkModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->showPkModal = true;
    }
    public function closePkModal() { $this->showPkModal = false; }

    public function openAksiModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->showAksiModal = true;
    }
    public function closeAksiModal() { $this->showAksiModal = false; }

    public function openBulananModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->bulanTerpilih = date('n'); 
        $this->showBulananModal = true;
    }
    public function closeBulananModal() { $this->showBulananModal = false; }

    public function openTahunanModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->showTahunanModal = true;
    }
    public function closeTahunanModal() { $this->showTahunanModal = false; }

    public function openEmonevModal() {
        $this->listJabatan = Jabatan::orderBy('id', 'asc')->get();
        $this->emonevTahun = date('Y');
        $this->emonevJabatan = '';
        $this->showEmonevModal = true;
    }
    public function closeEmonevModal() { $this->showEmonevModal = false; }

    // --- MODAL DATA PEGAWAI ---
    public function openPegawaiModal() {
        $this->listJabatan = Jabatan::orderBy('id', 'asc')->get();
        $this->pegawaiJabatan = ''; // Default: Semua Pegawai
        $this->showPegawaiModal = true;
    }
    public function closePegawaiModal() { $this->showPegawaiModal = false; }

    public function render()
    {
        return view('livewire.pusat-laporan')
            ->layout('components.layouts.app', ['title' => 'Pusat Laporan']);
    }
}