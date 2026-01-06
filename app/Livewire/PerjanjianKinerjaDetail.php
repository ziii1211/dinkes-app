<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PerjanjianKinerja;
use Carbon\Carbon; // TAMBAHKAN INI

class PerjanjianKinerjaDetail extends Component
{
    use WithPagination;

    public $jabatan;
    public $pegawai; 
    public $search = '';

    // --- MODAL PROPERTIES ---
    public $isOpen = false;
    public $tahun;
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
            // --- PERBAIKAN SORTING ---
            // Menggunakan 'id' 'asc' agar data baru (ID besar) masuk di urutan paling bawah
            ->orderBy('id', 'asc')
            ->paginate(10);

        // --- LOGIKA STATISTIK ---
        $totalPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)->count();
        
        // Hitung Draft (status_verifikasi = draft)
        $draftPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('status_verifikasi', 'draft')
            ->count();
            
        // Hitung Final (status_verifikasi = disetujui)
        $finalPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('status_verifikasi', 'disetujui')
            ->count();

        return view('livewire.perjanjian-kinerja-detail', [
            'pks' => $pks,
            'totalPk' => $totalPk,
            'draftPk' => $draftPk,
            'finalPk' => $finalPk
        ]);
    }

    public function openModal()
    {
        $this->reset(['keterangan']);
        $this->tahun = date('Y') + 1;
        $this->keterangan = "PK " . $this->jabatan->nama . " Tahun " . $this->tahun;

        $this->atasan_pegawai = null;
        $this->atasan_jabatan = null;

        if ($this->is_kepala_dinas) {
            // Gubernur logic (jika ada)
        } elseif ($this->jabatan->parent_id) {
            $parentJabatan = Jabatan::find($this->jabatan->parent_id);
            if ($parentJabatan) {
                $this->atasan_jabatan = $parentJabatan;
                $this->atasan_pegawai = Pegawai::where('jabatan_id', $parentJabatan->id)->latest()->first();
            }
        }

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function store()
    {
        $this->validate([
            'tahun' => 'required',
            'keterangan' => 'required',
        ]);

        // PERBAIKAN: Menggunakan Carbon::now('Asia/Makassar') untuk memaksa zona waktu WITA
        // Ini memastikan tanggal yang tersimpan adalah tanggal hari ini di Banjarmasin/Kalsel
        PerjanjianKinerja::create([
            'jabatan_id' => $this->jabatan->id,
            'pegawai_id' => $this->pegawai ? $this->pegawai->id : null,
            'tahun' => $this->tahun,
            'keterangan' => $this->keterangan,
            'status' => 'draft',
            'status_verifikasi' => 'draft', 
            'tanggal_penetapan' => Carbon::now('Asia/Makassar') 
        ]);

        $this->closeModal();

        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }
}