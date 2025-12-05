<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon;
use App\Models\IndikatorPohonKinerja;
use App\Models\SkpdTujuan; // BARU
use App\Models\CrosscuttingKinerja; // BARU

class PohonKinerja extends Component
{
    // --- STATES ---
    public $isOpen = false; 
    public $isOpenIndikator = false; 
    public $isOpenCrosscutting = false; // STATE BARU
    
    public $isChild = false;
    public $isEditMode = false;

    // --- FORM PROPERTIES POHON ---
    public $pohon_id; 
    public $tujuan_id;
    public $nama_pohon;
    public $parent_id;

    // --- FORM PROPERTIES INDIKATOR ---
    public $indikator_input; 
    public $indikator_list = []; 

    // --- FORM PROPERTIES CROSSCUTTING (BARU) ---
    public $cross_sumber_id;
    public $cross_skpd_id;
    public $cross_tujuan_id;

    public function render()
    {
        $treeData = $this->getFlatTree();

        return view('livewire.pohon-kinerja', [
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'pohons' => $treeData,
            
            // DATA BARU UNTUK CROSSCUTTING
            'skpds' => SkpdTujuan::all(),
            'all_pohons' => ModelPohon::all(), // Untuk dropdown sumber/tujuan
            'crosscuttings' => CrosscuttingKinerja::with(['pohonSumber', 'skpdTujuan', 'pohonTujuan'])->get()
        ]);
    }

    // ... (Fungsi getFlatTree, formatTree, openModal, addChild, edit, openIndikator, dll TETAP SAMA) ...
    
    private function getFlatTree()
    {
        $allNodes = ModelPohon::with(['tujuan', 'indikators'])->orderBy('created_at', 'asc')->get();
        $roots = $allNodes->whereNull('parent_id');
        $flatList = collect([]);
        foreach ($roots as $root) { $this->formatTree($root, $allNodes, $flatList, 0); }
        return $flatList;
    }

    private function formatTree($node, $allNodes, &$list, $depth)
    {
        $node->depth = $depth; $list->push($node);
        $children = $allNodes->where('parent_id', $node->id);
        foreach ($children as $child) { $this->formatTree($child, $allNodes, $list, $depth + 1); }
    }

    public function openModal() { 
        $this->reset(['tujuan_id', 'nama_pohon', 'parent_id', 'pohon_id', 'isChild', 'isEditMode']);
        $this->isOpen = true; 
    }

    public function addChild($parentId) {
        $this->reset(['nama_pohon', 'pohon_id']);
        $this->parent_id = $parentId;
        $parent = ModelPohon::find($parentId);
        $this->tujuan_id = $parent ? $parent->tujuan_id : null;
        $this->isChild = true; $this->isEditMode = false; $this->isOpen = true;
    }

    public function edit($id) {
        $pohon = ModelPohon::find($id);
        $this->pohon_id = $id; $this->tujuan_id = $pohon->tujuan_id; $this->nama_pohon = $pohon->nama_pohon; $this->parent_id = $pohon->parent_id;
        $this->isChild = $pohon->parent_id ? true : false; $this->isEditMode = true; $this->isOpen = true;
    }

    public function openIndikator($pohonId) {
        $this->pohon_id = $pohonId; $this->reset(['indikator_input', 'indikator_list']);
        $existingIndikators = IndikatorPohonKinerja::where('pohon_kinerja_id', $pohonId)->get();
        foreach($existingIndikators as $ind) { $this->indikator_list[] = ['id' => $ind->id, 'nama' => $ind->nama_indikator]; }
        $this->isOpenIndikator = true;
    }

    public function addIndikatorToList() {
        $this->validate(['indikator_input' => 'required']);
        $this->indikator_list[] = ['id' => 'temp_' . uniqid(), 'nama' => $this->indikator_input];
        $this->reset('indikator_input');
    }

    public function removeIndikatorFromList($index) {
        unset($this->indikator_list[$index]);
        $this->indikator_list = array_values($this->indikator_list);
    }

    public function saveIndikators() {
        IndikatorPohonKinerja::where('pohon_kinerja_id', $this->pohon_id)->delete();
        foreach($this->indikator_list as $ind) { IndikatorPohonKinerja::create(['pohon_kinerja_id' => $this->pohon_id, 'nama_indikator' => $ind['nama']]); }
        $this->closeModal();
    }

    public function closeModal() {
        $this->isOpen = false; $this->isOpenIndikator = false; $this->isOpenCrosscutting = false;
        $this->resetValidation();
    }

    public function store() {
        $rules = ['nama_pohon' => 'required'];
        if (!$this->isChild || $this->isEditMode) { $rules['tujuan_id'] = 'required'; }
        $this->validate($rules);
        if ($this->isEditMode) { ModelPohon::find($this->pohon_id)->update(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon]); } 
        else { ModelPohon::create(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon, 'parent_id' => $this->parent_id]); }
        $this->closeModal();
    }
    
    public function delete($id) { $pohon = ModelPohon::find($id); if($pohon) { $pohon->delete(); } }

    // --- ACTIONS CROSSCUTTING (BARU) ---
    
    public function openCrosscuttingModal()
    {
        $this->reset(['cross_sumber_id', 'cross_skpd_id', 'cross_tujuan_id']);
        $this->isOpenCrosscutting = true;
    }

    public function storeCrosscutting()
    {
        $this->validate([
            'cross_sumber_id' => 'required',
            'cross_skpd_id' => 'required',
            'cross_tujuan_id' => 'required',
        ]);

        CrosscuttingKinerja::create([
            'pohon_sumber_id' => $this->cross_sumber_id,
            'skpd_tujuan_id' => $this->cross_skpd_id,
            'pohon_tujuan_id' => $this->cross_tujuan_id,
        ]);

        $this->closeModal();
    }

    public function deleteCrosscutting($id)
    {
        $cc = CrosscuttingKinerja::find($id);
        if($cc) { $cc->delete(); }
    }
}