<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PerjanjianKinerja;
use Carbon\Carbon;

class PerjanjianKinerjaDetail extends Component
{
    use WithPagination;

    public $jabatan;
    public $pegawai; 
    public $search = '';

    // --- MODAL PROPERTIES ---
    public $isOpen = false;
    public $pkId = null; 
    public $tahun;
    public $bulan; // [PENTING] Sekarang dipakai lagi untuk simpan "Mulai Berlaku"
    public $keterangan;
    
    // Data Display di Modal
    public $atasan_pegawai;
    public $atasan_jabatan;
    
    // Properti Pihak 2 Khusus (Gubernur)
    public $is_kepala_dinas = false;
    public $gubernur_nama = 'H. MUHIDIN';
    public $gubernur_jabatan = 'GUBERNUR KALIMANTAN SELATAN';
    public $gubernur_foto = 'muhidin (1).png'; 

    public function mount($id)
    {
        $this->jabatan = Jabatan::findOrFail($id);
        $this->pegawai = Pegawai::where('jabatan_id', $id)->latest()->first();
        $this->tahun = date('Y') + 1;

        $this->is_kepala_dinas = is_null($this->jabatan->parent_id);
    }

    public function render()
    {
        $pks = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->when($this->search, function($q) {
                $q->where('keterangan', 'like', '%' . $this->search . '%');
            })
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc') // Urutkan biar PK Perubahan muncul paling atas
            ->paginate(10);

        // --- LOGIKA STATISTIK ---
        $totalPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)->count();
        $draftPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)->where('status_verifikasi', 'draft')->count();
        $finalPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)->where('status_verifikasi', 'disetujui')->count();

        return view('livewire.perjanjian-kinerja-detail', [
            'pks' => $pks,
            'totalPk' => $totalPk,
            'draftPk' => $draftPk,
            'finalPk' => $finalPk
        ]);
    }

    // --- BUKA MODAL TAMBAH (RESET FORM) ---
    public function openModal()
    {
        $this->reset(['keterangan', 'pkId']); 
        
        $this->tahun = date('Y');
        $this->bulan = 1; // Default Januari (Asumsi PK Murni)
        
        // Generate Keterangan Awal
        $this->generateKeterangan();

        // Load Data Atasan untuk Display Modal
        $this->loadAtasanData();

        $this->isOpen = true;
    }

    // --- BUKA MODAL EDIT ---
    public function edit($id)
    {
        $pk = PerjanjianKinerja::findOrFail($id);
        
        $this->pkId = $id; 
        $this->tahun = $pk->tahun;
        $this->bulan = $pk->bulan; // Load bulan yang tersimpan
        $this->keterangan = $pk->keterangan;

        // Load Data Atasan untuk Display Modal
        $this->loadAtasanData();

        $this->isOpen = true;
    }

    // Helper untuk load data atasan
    private function loadAtasanData()
    {
        $this->atasan_pegawai = null;
        $this->atasan_jabatan = null;

        if (!$this->is_kepala_dinas && $this->jabatan->parent_id) {
            $parentJabatan = Jabatan::find($this->jabatan->parent_id);
            if ($parentJabatan) {
                $this->atasan_jabatan = $parentJabatan;
                $this->atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
            }
        }
    }

    // Live Update Keterangan
    public function updatedBulan() { 
        if(!$this->pkId) $this->generateKeterangan(); 
    }
    
    public function updatedTahun() { 
        if(!$this->pkId) $this->generateKeterangan(); 
    }

    public function generateKeterangan()
    {
        // Fitur Pintar: Beda bulan, beda saran nama
        if ($this->bulan == 1) {
            // Jika Januari, biasanya Murni
            $this->keterangan = "PK " . $this->jabatan->nama . " Tahun " . $this->tahun;
        } else {
            // Jika bukan Januari, biasanya Perubahan
            $namaBulan = Carbon::createFromDate(null, $this->bulan, null)->locale('id')->translatedFormat('F');
            $this->keterangan = "PK Perubahan " . $this->jabatan->nama . " (Mulai " . $namaBulan . " " . $this->tahun . ")";
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['pkId', 'keterangan']); 
    }

    // --- LOGIKA PENYIMPANAN YANG DIPERBAIKI ---
    public function store()
    {
        $this->validate([
            'tahun' => 'required',
            'bulan' => 'required|integer|min:1|max:12', // [UPDATE] Validasi Bulan Wajib Ada
            'keterangan' => 'required',
        ]);

        // Siapkan Data
        $dataToSave = [
            'jabatan_id' => $this->jabatan->id,
            'pegawai_id' => $this->pegawai ? $this->pegawai->id : null,
            'tahun' => $this->tahun,
            
            // [UPDATE PENTING] Simpan nilai bulan yang dipilih user
            // Ini akan menjadi "Effective Start Date"
            'bulan' => $this->bulan, 
            
            'keterangan' => $this->keterangan,
        ];

        if ($this->pkId) {
            // MODE UPDATE
            $pk = PerjanjianKinerja::findOrFail($this->pkId);
            $pk->update($dataToSave);
        } else {
            // MODE CREATE
            $dataToSave['status'] = 'draft';
            $dataToSave['status_verifikasi'] = 'draft';
            $dataToSave['tanggal_penetapan'] = Carbon::now('Asia/Makassar');

            PerjanjianKinerja::create($dataToSave);
        }

        $this->closeModal();
        return redirect(request()->header('Referer'));
    }
}