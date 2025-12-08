<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon;
use App\Models\IndikatorPohonKinerja;
use App\Models\SkpdTujuan;
use App\Models\CrosscuttingKinerja;

class PohonKinerja extends Component
{
    // --- STATES (STATUS MODAL & MODE) ---
    public $isOpen = false; 
    public $isOpenIndikator = false; 
    public $isOpenCrosscutting = false;
    
    public $isChild = false;
    public $isEditMode = false;
    public $modeKinerjaUtama = false; // Flag khusus untuk Tombol Besar

    // --- FORM PROPERTIES (DATA POHON) ---
    public $pohon_id; 
    public $tujuan_id; 
    public $nama_pohon;
    public $parent_id;

    // --- PROPERTIES KHUSUS TOMBOL "TAMBAH KINERJA UTAMA" ---
    public $unit_kerja_id; 
    public $kinerja_utama_id; 

    // --- FORM PROPERTIES INDIKATOR ---
    public $indikator_input; 
    public $indikator_list = []; 

    // --- FORM PROPERTIES CROSSCUTTING ---
    public $cross_sumber_id;
    public $cross_skpd_id;
    public $cross_tujuan_id;

    public function render()
    {
        // Mengambil data tree yang sudah di-flatten (diratakan) untuk keperluan Tabel
        // Data ini juga bisa dipakai untuk visualisasi tree karena berisi Collection model lengkap
        $treeData = $this->getFlatTree();

        return view('livewire.pohon-kinerja', [
            // Data Utama untuk Tabel & Visualisasi Pohon
            'pohons' => $treeData,
            
            // Data Pendukung Dropdown
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'skpds' => SkpdTujuan::all(), // Untuk dropdown "Pilih Unit Kerja"
            'all_pohons' => ModelPohon::all(), // Untuk dropdown referensi "Pilih Kinerja"
            
            // Data Tabel Crosscutting
            'crosscuttings' => CrosscuttingKinerja::with(['pohonSumber', 'skpdTujuan', 'pohonTujuan'])->get()
        ]);
    }

    // --- LOGIKA UTAMA: MEMBUAT STRUKTUR DATA UNTUK TABEL & TREE ---
    private function getFlatTree()
    {
        // Ambil semua data, urutkan, dan eager load relasi agar hemat query
        $allNodes = ModelPohon::with(['tujuan', 'indikators'])->orderBy('created_at', 'asc')->get();
        
        // Cari node Root (yang tidak punya parent)
        $roots = $allNodes->whereNull('parent_id');
        
        $flatList = collect([]);
        foreach ($roots as $root) { 
            $this->formatTree($root, $allNodes, $flatList, 0); 
        }
        return $flatList;
    }

    // Fungsi rekursif untuk mengurutkan data agar tampil berjenjang di Tabel
    private function formatTree($node, $allNodes, &$list, $depth)
    {
        $node->depth = $depth; 
        $list->push($node);
        
        // Cari anak-anak dari node ini
        $children = $allNodes->where('parent_id', $node->id);
        
        foreach ($children as $child) { 
            $this->formatTree($child, $allNodes, $list, $depth + 1); 
        }
    }

    // --- HELPER: RESET FORM ---
    private function resetForm() {
        $this->reset([
            'tujuan_id', 'nama_pohon', 'parent_id', 'pohon_id', 
            'isChild', 'isEditMode', 'modeKinerjaUtama', 
            'unit_kerja_id', 'kinerja_utama_id'
        ]);
        $this->resetValidation();
    }

    // --- MANAJEMEN MODAL ---

    // 1. Buka Modal Manual (Tombol "Buat Pohon" di atas Tabel)
    public function openModal() { 
        $this->resetForm();
        $this->isOpen = true; 
    }

    // 2. Buka Modal Tombol Besar ("Tambah Kinerja Utama")
    public function openModalKinerjaUtama() {
        $this->resetForm();
        $this->modeKinerjaUtama = true; // Aktifkan mode khusus
        $this->isOpen = true;
    }

    // 3. Buka Modal Tambah Turunan (Tombol "+ Turunan" di Kartu/Tabel)
    public function addChild($parentId) {
        $this->resetForm();
        $this->parent_id = $parentId;
        $this->isChild = true; 
        
        // Otomatis mewarisi Sasaran RPJMD dari parent (jika ada)
        $parent = ModelPohon::find($parentId);
        $this->tujuan_id = $parent ? $parent->tujuan_id : null;
        
        $this->isOpen = true;
    }

    // 4. Buka Modal Edit
    public function edit($id) {
        $this->resetForm();
        $pohon = ModelPohon::find($id);
        
        $this->pohon_id = $id; 
        $this->tujuan_id = $pohon->tujuan_id; 
        $this->nama_pohon = $pohon->nama_pohon; 
        $this->parent_id = $pohon->parent_id;
        
        $this->isChild = $pohon->parent_id ? true : false; 
        $this->isEditMode = true; 
        
        $this->isOpen = true;
    }

    public function closeModal() {
        $this->isOpen = false; 
        $this->isOpenIndikator = false; 
        $this->isOpenCrosscutting = false;
        $this->resetValidation();
    }

    // --- FUNGSI STORE (SIMPAN DATA) ---
    public function store() {
        
        // SKENARIO A: Input lewat Tombol Besar "Tambah Kinerja Utama"
        if ($this->modeKinerjaUtama) {
            $this->validate([
                'unit_kerja_id' => 'required', // Wajib pilih Unit Kerja
                'kinerja_utama_id' => 'required' // Wajib pilih Kinerja Referensi
            ]);

            // Ambil Nama dari Referensi Kinerja yang dipilih untuk dijadikan nama_pohon baru
            $ref = ModelPohon::find($this->kinerja_utama_id);
            $namaBaru = $ref ? $ref->nama_pohon : '-';

            // Simpan Data Baru
            // Catatan: Saat ini 'unit_kerja_id' hanya divalidasi tapi belum disimpan ke DB
            // karena tabel pohon_kinerjas defaultnya tidak punya kolom skpd_id.
            // Data disimpan sebagai Root baru (parent_id = null).
            ModelPohon::create([
                'tujuan_id' => $ref->tujuan_id ?? null, // Mewarisi tujuan dari referensi jika ada
                'nama_pohon' => $namaBaru,
                'parent_id' => null
            ]);
        } 
        
        // SKENARIO B: Edit Data Existing
        elseif ($this->isEditMode) { 
            $this->validate(['nama_pohon' => 'required']);
            
            ModelPohon::find($this->pohon_id)->update([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon
            ]); 
        } 
        
        // SKENARIO C: Input Manual Biasa (Root / Child)
        else { 
            $rules = ['nama_pohon' => 'required'];
            // Jika Root manual, wajib pilih Sasaran RPJMD
            if (!$this->isChild) { $rules['tujuan_id'] = 'required'; } 
            
            $this->validate($rules);

            ModelPohon::create([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon, 
                'parent_id' => $this->parent_id // null jika root, terisi jika child
            ]); 
        }
        
        $this->closeModal();
    }

    // --- MANAJEMEN INDIKATOR (TIDAK BERUBAH) ---
    public function openIndikator($pohonId) {
        $this->pohon_id = $pohonId; 
        $this->reset(['indikator_input', 'indikator_list']);
        
        $existing = IndikatorPohonKinerja::where('pohon_kinerja_id', $pohonId)->get();
        foreach($existing as $ind) { 
            $this->indikator_list[] = ['id' => $ind->id, 'nama' => $ind->nama_indikator]; 
        }
        $this->isOpenIndikator = true;
    }

    public function addIndikatorToList() {
        $this->validate(['indikator_input' => 'required']);
        // ID temp unik untuk handling di frontend sebelum save
        $this->indikator_list[] = ['id' => 'temp_' . uniqid(), 'nama' => $this->indikator_input];
        $this->reset('indikator_input');
    }

    public function removeIndikatorFromList($index) {
        unset($this->indikator_list[$index]);
        $this->indikator_list = array_values($this->indikator_list); // Re-index array
    }

    public function saveIndikators() {
        // Hapus indikator lama, ganti dengan yang baru (cara sederhana handling relasi hasMany)
        IndikatorPohonKinerja::where('pohon_kinerja_id', $this->pohon_id)->delete();
        
        foreach($this->indikator_list as $ind) { 
            IndikatorPohonKinerja::create([
                'pohon_kinerja_id' => $this->pohon_id, 
                'nama_indikator' => $ind['nama']
            ]); 
        }
        $this->closeModal();
    }

    // --- MANAJEMEN DATA POHON (HAPUS) ---
    public function delete($id) { 
        $pohon = ModelPohon::find($id); 
        if($pohon) { $pohon->delete(); } 
    }

    // --- MANAJEMEN CROSSCUTTING (TIDAK BERUBAH) ---
    public function openCrosscuttingModal() { 
        $this->reset(['cross_sumber_id', 'cross_skpd_id', 'cross_tujuan_id']); 
        $this->isOpenCrosscutting = true; 
    }

    public function storeCrosscutting() {
        $this->validate([
            'cross_sumber_id' => 'required', 
            'cross_skpd_id' => 'required', 
            'cross_tujuan_id' => 'required'
        ]);
        
        CrosscuttingKinerja::create([
            'pohon_sumber_id' => $this->cross_sumber_id, 
            'skpd_tujuan_id' => $this->cross_skpd_id, 
            'pohon_tujuan_id' => $this->cross_tujuan_id
        ]);
        
        $this->closeModal();
    }

    public function deleteCrosscutting($id) { 
        $cc = CrosscuttingKinerja::find($id); 
        if($cc) { $cc->delete(); } 
    }
}