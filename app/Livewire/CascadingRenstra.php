<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon; 
use App\Models\IndikatorPohonKinerja;
// use App\Models\SkpdTujuan; // Dihapus karena hanya dipakai di crosscutting
// use App\Models\CrosscuttingKinerja; // Dihapus karena tabel dihapus

class CascadingRenstra extends Component
{
    use WithPagination;

    // --- STATES ---
    public $isOpen = false; 
    public $isOpenIndikator = false; 
    // $isOpenCrosscutting dihapus
    
    public $isChild = false;
    public $isEditMode = false;

    // --- FORM PROPERTIES ---
    public $pohon_id; 
    
    public $tujuan_id; 
    public $nama_pohon;
    public $parent_id;

    // Properties Indikator
    public $indikator_input; 
    public $indikator_nilai;
    public $indikator_satuan;
    public $indikator_list = []; 

    // Properties Crosscutting dihapus ($cross_sumber_id, dll)

    public function render()
    {
        // 1. Ambil Struktur Pohon untuk Visualisasi (Induk -> Anak -> Cucu)
        $treeData = $this->getFlatTree();

        // 2. Ambil Data Master untuk Dropdown (Parent Selection)
        $masterPohons = ModelPohon::with('tujuan')
                                ->orderBy('id', 'asc')
                                ->get();

        // Data Crosscutting dan SKPD dihapus dari sini

        return view('livewire.cascading-renstra', [
            'pohons' => $treeData, 
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'opsiPohon' => $masterPohons, 
        ]);
    }

    private function getFlatTree()
    {
        // Ambil semua node beserta relasinya
        $allNodes = ModelPohon::with([
                        'tujuan', 
                        'indikators', 
                        'children.indikators', 
                        'children.children.indikators' 
                    ])
                    ->orderBy('created_at', 'asc')
                    ->get();
                    
        // Root adalah yang parent_id nya null
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
            'isChild', 'isEditMode', 
            // Reset variables crosscutting dihapus
            'indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list'
        ]);
        $this->resetValidation();
    }

    public function openModal() { 
        $this->resetForm();
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
        // $isOpenCrosscutting dihapus
        $this->resetValidation();
    }

    // --- SIMPAN DATA ---
    public function store() {
        // Validasi Umum
        $rules = ['nama_pohon' => 'required'];
        // Jika Root (bukan anak), wajib pilih Sasaran RPJMD
        if (!$this->isChild && !$this->parent_id) { 
            $rules['tujuan_id'] = 'required'; 
        } 
        
        $this->validate($rules);

        if ($this->isEditMode) { 
            ModelPohon::find($this->pohon_id)->update([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon
            ]); 
        } else { 
            ModelPohon::create([
                'tujuan_id' => $this->tujuan_id, 
                'nama_pohon' => $this->nama_pohon, 
                'parent_id' => $this->parent_id 
            ]); 
        }
        
        $this->closeModal();
        session()->flash('message', 'Data Cascading Renstra berhasil disimpan.');
    }

    public function delete($id) { 
        $pohon = ModelPohon::find($id); 
        if($pohon) { 
            $pohon->delete(); 
            session()->flash('message', 'Cascading Renstra berhasil dihapus.');
        } 
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

    // Method Crosscutting (openCrosscuttingModal, storeCrosscutting, deleteCrosscutting) TELAH DIHAPUS
}