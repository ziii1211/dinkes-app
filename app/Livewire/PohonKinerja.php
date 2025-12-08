<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon;
use App\Models\IndikatorPohonKinerja;
use App\Models\SkpdTujuan;
use App\Models\CrosscuttingKinerja;

class PohonKinerja extends Component
{
    use WithPagination;

    // --- STATES (STATUS MODAL & MODE) ---
    public $isOpen = false; 
    public $isOpenIndikator = false; 
    public $isOpenCrosscutting = false;
    public $isOpenManageCrosscutting = false; 
    
    public $isChild = false;
    public $isEditMode = false;
    public $modeKinerjaUtama = false; 

    // --- FORM PROPERTIES POHON KINERJA ---
    public $pohon_id; 
    public $active_pohon_id; // ID Pohon konteks (Parent) saat klik tombol
    
    public $tujuan_id; 
    public $nama_pohon;
    public $parent_id;

    public $unit_kerja_id; 
    public $kinerja_utama_id; 

    // Properties Indikator
    public $indikator_input; 
    public $indikator_nilai;
    public $indikator_satuan;
    public $indikator_list = []; 

    // --- PROPERTIES CROSSCUTTING ---
    public $cross_sumber_id;    
    public $cross_skpd_id;      
    public $cross_tujuan_id;    

    public function render()
    {
        // 1. Ambil Struktur Pohon (Induk -> Anak -> Cucu)
        $treeData = $this->getFlatTree();

        // 2. PERBAIKAN UTAMA: Filter Opsi Dropdown
        // Hanya ambil data yang 'jenis'-nya KOSONG (Manual) atau BUKAN visualisasi/crosscutting
        // Ini akan mencegah duplikat di dropdown 'Pilih Kinerja Sumber' & 'Referensi'
        $masterPohons = ModelPohon::where(function($query) {
                                    $query->whereNull('jenis')
                                          ->orWhereNotIn('jenis', ['visualisasi', 'crosscutting', 'hide']);
                                })
                                ->orderBy('nama_pohon', 'asc')
                                ->get();

        // 3. Ambil Opsi SKPD
        $opsiSkpd = SkpdTujuan::orderBy('nama_skpd', 'asc')->get();

        // 4. Ambil Data Tabel Crosscutting
        $crosscuttings = CrosscuttingKinerja::with(['pohonSumber', 'skpdTujuan', 'pohonTujuan'])
                                            ->latest()
                                            ->paginate(5);

        return view('livewire.pohon-kinerja', [
            'pohons' => $treeData,
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'skpds' => SkpdTujuan::all(),
            'all_pohons' => ModelPohon::all(), // Data mentah jika dibutuhkan view lain
            
            // GUNAKAN VARIABEL INI UNTUK DROPDOWN AGAR TIDAK DUPLIKAT
            'opsiMasterPohon' => $masterPohons, 
            'opsiSkpd' => $opsiSkpd,
            
            // Variabel khusus agar sesuai dengan View yang sudah kita perbaiki sebelumnya
            // Kita override $opsiPohon dan $all_pohons (yang dipakai di dropdown) dengan data master
            'opsiPohon' => $masterPohons, 
            'all_pohons' => $masterPohons, 

            'crosscuttings' => $crosscuttings
        ]);
    }

    // --- LOGIKA UTAMA POHON (FLAT TREE) ---
    private function getFlatTree()
    {
        $allNodes = ModelPohon::with([
                        'tujuan', 
                        'indikators', 
                        'children.indikators', 
                        'children.children.indikators' 
                    ])
                    ->orderBy('created_at', 'asc')
                    ->get();
                    
        $roots = $allNodes->whereNull('parent_id');
        
        $flatList = collect([]);
        foreach ($roots as $root) { 
            $this->formatTree($root, $allNodes, $flatList, 0); 
        }
        return $flatList;
    }

    private function formatTree($node, $allNodes, &$list, $depth)
    {
        $node->depth = $depth; 
        $list->push($node);
        $children = $allNodes->where('parent_id', $node->id);
        foreach ($children as $child) { 
            $this->formatTree($child, $allNodes, $list, $depth + 1); 
        }
    }

    private function resetForm() {
        $this->reset([
            'tujuan_id', 'nama_pohon', 'parent_id', 'pohon_id', 'active_pohon_id',
            'isChild', 'isEditMode', 'modeKinerjaUtama', 
            'unit_kerja_id', 'kinerja_utama_id',
            'cross_sumber_id', 'cross_skpd_id', 'cross_tujuan_id',
            'indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list'
        ]);
        $this->resetValidation();
    }

    // --- MANAJEMEN MODAL UTAMA ---
    public function openModal() { 
        $this->resetForm();
        $this->isOpen = true; 
    }

    public function openModalKinerjaUtama() {
        $this->resetForm();
        $this->modeKinerjaUtama = true;
        $this->isOpen = true;
    }

    public function addChild($parentId) {
        $this->resetForm();
        $this->parent_id = $parentId;
        $this->isChild = true; 
        $parent = ModelPohon::find($parentId);
        $this->tujuan_id = $parent ? $parent->tujuan_id : null;
        $this->isOpen = true;
    }

    public function edit($id) {
        $this->resetForm();
        $pohon = ModelPohon::find($id);
        if ($pohon) {
            $this->pohon_id = $id; 
            $this->tujuan_id = $pohon->tujuan_id; 
            $this->nama_pohon = $pohon->nama_pohon; 
            $this->parent_id = $pohon->parent_id;
            $this->isChild = $pohon->parent_id ? true : false; 
            $this->isEditMode = true; 
            $this->isOpen = true;
        }
    }

    public function closeModal() {
        $this->isOpen = false; 
        $this->isOpenIndikator = false; 
        $this->isOpenCrosscutting = false;
        $this->isOpenManageCrosscutting = false; 
        $this->resetValidation();
    }

    // --- FUNGSI SIMPAN POHON KINERJA ---
    public function store() {
        if ($this->modeKinerjaUtama) {
            // LOGIC VISUALISASI
            $this->validate([
                'unit_kerja_id' => 'required',
                'kinerja_utama_id' => 'required'
            ]);

            $ref = ModelPohon::with('indikators')->find($this->kinerja_utama_id);
            $namaBaru = $ref ? $ref->nama_pohon : '-';
            
            // Buat Node Visualisasi (Root/Parent)
            $newPohon = ModelPohon::create([
                'tujuan_id' => $ref->tujuan_id ?? null,
                'nama_pohon' => $namaBaru,
                'parent_id' => null, 
                'jenis' => 'visualisasi' 
            ]);

            // Salin Indikator
            if ($ref && $ref->indikators->count() > 0) {
                foreach ($ref->indikators as $ind) {
                    IndikatorPohonKinerja::create([
                        'pohon_kinerja_id' => $newPohon->id, 
                        'nama_indikator' => $ind->nama_indikator,
                        'target' => $ind->target,
                        'satuan' => $ind->satuan
                    ]);
                }
            }

        } elseif ($this->isEditMode) { 
            // LOGIC EDIT
            $this->validate(['nama_pohon' => 'required']);
            ModelPohon::find($this->pohon_id)->update([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon
            ]); 
        } else { 
            // LOGIC SIMPAN MANUAL (JENIS = NULL)
            $rules = ['nama_pohon' => 'required'];
            if (!$this->isChild) { $rules['tujuan_id'] = 'required'; } 
            $this->validate($rules);
            
            ModelPohon::create([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon, 
                'parent_id' => $this->parent_id 
                // 'jenis' dibiarkan NULL, menandakan ini DATA MASTER
            ]); 
        }
        $this->closeModal();
        session()->flash('message', 'Data Pohon Kinerja berhasil disimpan.');
    }

    // --- INDIKATOR ---
    public function openIndikator($pohonId) {
        $this->pohon_id = $pohonId; 
        $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list']);
        $existing = IndikatorPohonKinerja::where('pohon_kinerja_id', $pohonId)->get();
        foreach($existing as $ind) { 
            $this->indikator_list[] = [
                'id' => $ind->id, 
                'nama' => $ind->nama_indikator,
                'nilai' => $ind->target ?? 0, 
                'satuan' => $ind->satuan ?? '-'
            ]; 
        }
        $this->isOpenIndikator = true;
    }

    public function addIndikatorToList() {
        $this->validate([
            'indikator_input' => 'required',
            'indikator_nilai' => 'required',
            'indikator_satuan' => 'required',
        ]);
        $this->indikator_list[] = [
            'id' => 'temp_' . uniqid(), 
            'nama' => $this->indikator_input,
            'nilai' => $this->indikator_nilai,
            'satuan' => $this->indikator_satuan
        ];
        $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan']);
    }

    public function removeIndikatorFromList($index) {
        unset($this->indikator_list[$index]);
        $this->indikator_list = array_values($this->indikator_list); 
    }

    public function saveIndikators() {
        IndikatorPohonKinerja::where('pohon_kinerja_id', $this->pohon_id)->delete();
        foreach($this->indikator_list as $ind) { 
            IndikatorPohonKinerja::create([
                'pohon_kinerja_id' => $this->pohon_id, 
                'nama_indikator' => $ind['nama'],
                'target' => $ind['nilai'],   
                'satuan' => $ind['satuan']   
            ]); 
        }
        $this->closeModal();
        session()->flash('message', 'Indikator berhasil disimpan.');
    }

    public function delete($id) { 
        $pohon = ModelPohon::find($id); 
        if($pohon) { 
            $pohon->delete(); 
            session()->flash('message', 'Pohon Kinerja berhasil dihapus.');
        } 
    }

    // --- HAPUS VISUALISASI ---
    public function deleteKinerjaUtama($id) {
        $pohon = ModelPohon::find($id);
        if($pohon) {
            if ($pohon->jenis === 'visualisasi' || $pohon->jenis === 'crosscutting') {
                $pohon->delete();
            } else {
                $pohon->update(['jenis' => 'hide']); 
            }
            session()->flash('message', 'Item berhasil dihapus.');
        }
    }

    // --- CROSSCUTTING FUNCTIONS ---

    public function openCrosscuttingModal($pohonId = null) { 
        $this->reset(['cross_sumber_id', 'cross_skpd_id', 'cross_tujuan_id', 'active_pohon_id']); 
        
        if($pohonId) {
            $this->active_pohon_id = $pohonId;
            $this->cross_sumber_id = $pohonId; 
        }

        $this->isOpenCrosscutting = true; 
    }

    public function openManageCrosscutting($pohonId) {
        $this->pohon_id = $pohonId; 
        $this->isOpenManageCrosscutting = true;
    }

    public function storeCrosscutting() {
        $this->validate([
            'cross_sumber_id' => 'required', 
            'cross_skpd_id' => 'required', 
            'cross_tujuan_id' => 'required'
        ]);

        // 1. Simpan Data Relasi
        CrosscuttingKinerja::create([
            'pohon_sumber_id' => $this->cross_sumber_id, 
            'skpd_tujuan_id' => $this->cross_skpd_id, 
            'pohon_tujuan_id' => $this->cross_tujuan_id
        ]);

        // 2. Logic Visualisasi (Jika dibuka dari diagram)
        if ($this->active_pohon_id) {
            $targetPohon = ModelPohon::with('indikators')->find($this->cross_tujuan_id);
            
            if($targetPohon) {
                // Buat Node Visualisasi Anak
                $childNode = ModelPohon::create([
                    'parent_id' => $this->active_pohon_id, 
                    'tujuan_id' => $targetPohon->tujuan_id,
                    'nama_pohon' => $targetPohon->nama_pohon, 
                    'jenis' => 'crosscutting' // Penanda jenis visualisasi
                ]);

                // Salin Indikator
                if ($targetPohon->indikators->count() > 0) {
                    foreach($targetPohon->indikators as $ind) {
                        IndikatorPohonKinerja::create([
                            'pohon_kinerja_id' => $childNode->id, 
                            'nama_indikator' => $ind->nama_indikator,
                            'target' => $ind->target,
                            'satuan' => $ind->satuan
                        ]);
                    }
                }
            }
        }
        
        $this->closeModal();
        session()->flash('message', 'Data Crosscutting berhasil ditambahkan.');
    }

    public function deleteCrosscutting($id) { 
        $cc = CrosscuttingKinerja::find($id); 
        if($cc) { 
            $cc->delete(); 
            session()->flash('message', 'Data Crosscutting berhasil dihapus.');
        } 
    }
}