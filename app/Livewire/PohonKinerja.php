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
    public $isOpenManageCrosscutting = false; // BARU: State untuk modal hapus crosscutting
    
    public $isChild = false;
    public $isEditMode = false;
    public $modeKinerjaUtama = false; 

    // --- FORM PROPERTIES ---
    public $pohon_id; 
    public $tujuan_id; 
    public $nama_pohon;
    public $parent_id;

    public $unit_kerja_id; 
    public $kinerja_utama_id; 

    public $indikator_input; 
    public $indikator_nilai;
    public $indikator_satuan;
    public $indikator_list = []; 

    public $cross_sumber_id;
    public $cross_skpd_id;
    public $cross_tujuan_id;

    public function render()
    {
        $treeData = $this->getFlatTree();

        return view('livewire.pohon-kinerja', [
            'pohons' => $treeData,
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'skpds' => SkpdTujuan::all(),
            'all_pohons' => ModelPohon::all(),
            'crosscuttings' => CrosscuttingKinerja::with(['pohonSumber', 'skpdTujuan', 'pohonTujuan'])->get()
        ]);
    }

    // --- LOGIKA UTAMA ---
    private function getFlatTree()
    {
        $allNodes = ModelPohon::with(['tujuan', 'indikators'])->orderBy('created_at', 'asc')->get();
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
            'tujuan_id', 'nama_pohon', 'parent_id', 'pohon_id', 
            'isChild', 'isEditMode', 'modeKinerjaUtama', 
            'unit_kerja_id', 'kinerja_utama_id'
        ]);
        $this->resetValidation();
    }

    // --- MANAJEMEN MODAL ---
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
        $this->isOpenManageCrosscutting = false; // Reset modal manage
        $this->resetValidation();
    }

    public function store() {
        if ($this->modeKinerjaUtama) {
            $this->validate([
                'unit_kerja_id' => 'required',
                'kinerja_utama_id' => 'required'
            ]);
            $ref = ModelPohon::find($this->kinerja_utama_id);
            $namaBaru = $ref ? $ref->nama_pohon : '-';
            ModelPohon::create([
                'tujuan_id' => $ref->tujuan_id ?? null,
                'nama_pohon' => $namaBaru,
                'parent_id' => null
            ]);
        } elseif ($this->isEditMode) { 
            $this->validate(['nama_pohon' => 'required']);
            ModelPohon::find($this->pohon_id)->update([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon
            ]); 
        } else { 
            $rules = ['nama_pohon' => 'required'];
            if (!$this->isChild) { $rules['tujuan_id'] = 'required'; } 
            $this->validate($rules);
            ModelPohon::create([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon, 
                'parent_id' => $this->parent_id 
            ]); 
        }
        $this->closeModal();
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
    }

    public function delete($id) { 
        $pohon = ModelPohon::find($id); 
        if($pohon) { $pohon->delete(); } 
    }

    // --- FUNGSI BARU: Hapus Kinerja Utama (Hanya Hide) ---
    // Fungsi ini mengubah kolom 'jenis' menjadi 'hide' agar tidak muncul di visualisasi
    // namun data tetap ada di tabel utama.
    public function deleteKinerjaUtama($id) {
        $pohon = ModelPohon::find($id);
        if($pohon) {
            $pohon->update(['jenis' => 'hide']); 
        }
    }

    // --- CROSSCUTTING ---
    public function openCrosscuttingModal() { 
        $this->reset(['cross_sumber_id', 'cross_skpd_id', 'cross_tujuan_id']); 
        $this->isOpenCrosscutting = true; 
    }

    // BARU: Fungsi untuk membuka modal LIST crosscutting agar bisa dihapus
    public function openManageCrosscutting($pohonId) {
        $this->pohon_id = $pohonId; // Simpan ID pohon yg sedang dikelola
        $this->isOpenManageCrosscutting = true;
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