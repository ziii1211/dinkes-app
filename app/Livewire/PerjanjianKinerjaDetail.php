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
    public $pkId = null; // [BARU] Untuk menyimpan ID saat Edit
    public $tahun;
    public $bulan;
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
            ->orderBy('id', 'asc')
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
        // [UPDATE] Reset pkId agar sistem tahu ini mode tambah baru
        $this->reset(['keterangan', 'pkId']); 
        
        $this->tahun = date('Y');
        $this->bulan = (int) date('n');
        
        // Generate Keterangan Awal
        $this->generateKeterangan();

        // Load Data Atasan untuk Display Modal
        $this->loadAtasanData();

        $this->isOpen = true;
    }

    // --- [BARU] BUKA MODAL EDIT ---
    public function edit($id)
    {
        $pk = PerjanjianKinerja::findOrFail($id);
        
        $this->pkId = $id; // Set ID yang sedang diedit
        $this->tahun = $pk->tahun;
        $this->bulan = $pk->bulan;
        $this->keterangan = $pk->keterangan;

        // Load Data Atasan untuk Display Modal
        $this->loadAtasanData();

        $this->isOpen = true;
    }

    // Helper untuk load data atasan (biar tidak duplikat kode)
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

    // Live Update Keterangan (Hanya jalan jika mode Tambah / pkId null)
    // Agar saat edit, keterangan user tidak tertimpa otomatis jika mereka ganti tahun/bulan
    public function updatedBulan() { 
        if(!$this->pkId) $this->generateKeterangan(); 
    }
    public function updatedTahun() { 
        if(!$this->pkId) $this->generateKeterangan(); 
    }

    public function generateKeterangan()
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $namaBulan = $months[$this->bulan] ?? '';
        $this->keterangan = "PK " . $this->jabatan->nama . " Bulan " . $namaBulan . " " . $this->tahun;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['pkId', 'keterangan']); // Bersihkan ID saat tutup
    }

    public function store()
    {
        $this->validate([
            'tahun' => 'required',
            'bulan' => 'required|integer|min:1|max:12',
            'keterangan' => 'required',
        ]);

        // [UPDATE] Logika Simpan atau Update
        if ($this->pkId) {
            // MODE UPDATE
            $pk = PerjanjianKinerja::findOrFail($this->pkId);
            $pk->update([
                'tahun' => $this->tahun,
                'bulan' => $this->bulan,
                'keterangan' => $this->keterangan,
            ]);
        } else {
            // MODE CREATE (TAMBAH BARU)
            PerjanjianKinerja::create([
                'jabatan_id' => $this->jabatan->id,
                'pegawai_id' => $this->pegawai ? $this->pegawai->id : null,
                'tahun' => $this->tahun,
                'bulan' => $this->bulan,
                'keterangan' => $this->keterangan,
                'status' => 'draft',
                'status_verifikasi' => 'draft', 
                'tanggal_penetapan' => Carbon::now('Asia/Makassar') 
            ]);
        }

        $this->closeModal();
        return redirect(request()->header('Referer'));
    }
}