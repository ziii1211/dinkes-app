<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon;      // Model Lama (Tabel Bawah)
use App\Models\VisualisasiRenstra;              // Model Baru (Visualisasi)
use App\Models\IndikatorPohonKinerja;
use App\Models\Jabatan;

class CascadingRenstra extends Component
{
    use WithPagination;

    // ==========================================
    // 1. STATES & PROPERTIES
    // ==========================================

    // UI States
    public $isOpen = false;
    public $isOpenIndikator = false;
    public $isChild = false;
    public $isEditMode = false;
    public $modalPreviewOpen = false;

    // Properties DB Lama (Legacy - Tabel Bawah)
    public $pohon_id, $tujuan_id, $nama_pohon, $parent_id;
    
    // Properties Indikator (UPDATED: Hapus Nilai & Satuan)
    public $indikator_input; 
    public $indikator_list = [];

    // Properties Visualisasi (Data Utama Canvas)
    public $visualNodes = [];

    // ==========================================
    // 2. LIFECYCLE: MOUNT & RENDER
    // ==========================================

    public function mount()
    {
        $this->loadVisualData();
    }

    public function render()
    {
        // 1. Build Tree untuk Visualisasi
        $manualTree = $this->buildVisualTreeStructure();

        // 2. Data Dropdown Jabatan
        $listJabatans = Jabatan::orderBy('level', 'asc')->orderBy('id', 'asc')->get();

        // 3. Data Tabel Lama (Bawah)
        $treeData = $this->getFlatTree(); 
        $masterPohons = ModelPohon::with('tujuan')->orderBy('id', 'asc')->get();

        return view('livewire.cascading-renstra', [
            'manualTree' => $manualTree,
            'listJabatans' => $listJabatans,
            'pohons' => $treeData, 
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'opsiPohon' => $masterPohons,
        ]);
    }

    // ==========================================
    // 3. ACTIONS: PREVIEW MODAL
    // ==========================================

    public function openPreview() { $this->modalPreviewOpen = true; }
    public function closePreview() { $this->modalPreviewOpen = false; }

    // ==========================================
    // 4. DATA LOADING (VISUALISASI)
    // ==========================================

    public function loadVisualData()
    {
        $allNodes = VisualisasiRenstra::orderBy('id', 'asc')->get();
        $this->visualNodes = [];

        if ($allNodes->count() > 0) {
            foreach ($allNodes as $node) {
                $this->visualNodes[] = $this->formatNodeFlat($node);
            }
        } else {
            $this->addManualRoot();
        }
    }

    private function formatNodeFlat($dbNode)
    {
        $items = $dbNode->content_data;
        if(is_string($items)) { $items = json_decode($items, true); }
        if(empty($items) || !is_array($items)) { $items = [['kinerja_utama' => '', 'indikators' => []]]; }

        return [
            'id' => $dbNode->id,
            'parent_id' => $dbNode->parent_id,
            'jabatan' => $dbNode->jabatan,
            'is_locked' => $dbNode->is_locked,
            'kinerja_items' => $items,
        ];
    }

    // ==========================================
    // 5. REAL-TIME AUTO SAVE
    // ==========================================

    public function updated($property)
    {
        if (str_starts_with($property, 'visualNodes.')) {
            $parts = explode('.', $property);
            $index = $parts[1] ?? null;

            if ($index !== null && is_numeric($index)) {
                if (isset($this->visualNodes[$index]['id']) && is_numeric($this->visualNodes[$index]['id'])) {
                    $this->saveNodeData($index, true);
                }
            }
        }
    }

    // ==========================================
    // 6. SAVE & UPDATE LOGIC (VISUALISASI)
    // ==========================================

    public function saveNodeData($nodeIndex, $silent = false)
    {
        $data = $this->visualNodes[$nodeIndex];
        $parentIdToSave = ($data['parent_id'] && is_numeric($data['parent_id'])) ? $data['parent_id'] : null;
        $node = is_numeric($data['id']) ? VisualisasiRenstra::find($data['id']) : null;

        if($node) {
            $node->update([
                'jabatan' => $data['jabatan'],
                'content_data' => $data['kinerja_items'],
                'is_locked' => true
            ]);
        } else {
            $newNode = VisualisasiRenstra::create([
                'parent_id' => $parentIdToSave,
                'jabatan' => $data['jabatan'],
                'content_data' => $data['kinerja_items'],
                'is_locked' => true
            ]);
            
            $oldId = $this->visualNodes[$nodeIndex]['id'];
            $this->visualNodes[$nodeIndex]['id'] = $newNode->id;
            $this->visualNodes[$nodeIndex]['is_locked'] = true;

            foreach($this->visualNodes as $key => $vNode) {
                if($vNode['parent_id'] === $oldId) {
                    $this->visualNodes[$key]['parent_id'] = $newNode->id;
                }
            }
        }
        if(!$silent) session()->flash('message', 'Data tersimpan otomatis!');
    }

    public function lockNode($nodeIndex) 
    { 
        if(empty($this->visualNodes[$nodeIndex]['jabatan'])) return;
        $this->saveNodeData($nodeIndex); 
    }

    public function unlockNode($nodeIndex) 
    { 
        $this->visualNodes[$nodeIndex]['is_locked'] = false; 
    }

    // ==========================================
    // 7. TREE STRUCTURE BUILDER
    // ==========================================

    private function buildVisualTreeStructure()
    {
        $nodes = collect($this->visualNodes)->map(function($item, $key) {
            $item['original_index'] = $key;
            return (object) $item;
        });

        $nodesDict = $nodes->keyBy('id');
        foreach($nodes as $node) { $node->children = collect([]); }
        $tree = collect([]);
        
        foreach($nodes as $node) {
            if($node->parent_id && isset($nodesDict[$node->parent_id])) {
                $nodesDict[$node->parent_id]->children->push($node);
            } else {
                $tree->push($node);
            }
        }
        return $tree;
    }

    // ==========================================
    // 8. CRUD ACTIONS (VISUALISASI)
    // ==========================================

    public function addManualRoot()
    {
        $this->visualNodes[] = ['id' => 'temp_' . uniqid(), 'parent_id' => null, 'jabatan' => '', 'is_locked' => false, 'kinerja_items' => [['kinerja_utama' => '', 'indikators' => []]]];
    }

    public function addManualChild($parentId)
    {
        if(!is_numeric($parentId)) { session()->flash('error', 'Simpan Parent terlebih dahulu!'); return; }
        $this->visualNodes[] = ['id' => 'temp_' . uniqid(), 'parent_id' => $parentId, 'jabatan' => '', 'is_locked' => false, 'kinerja_items' => [['kinerja_utama' => '', 'indikators' => []]]];
    }

    public function deleteManualNode($id)
    {
        if(is_numeric($id)) { VisualisasiRenstra::destroy($id); }
        $this->loadVisualData();
    }

    // --- ITEM ACTIONS (KINERJA & INDIKATOR VISUAL) ---
    public function addKinerjaItem($nodeIndex) { $this->visualNodes[$nodeIndex]['kinerja_items'][] = ['kinerja_utama' => '', 'indikators' => []]; if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true); }
    public function removeKinerjaItem($nodeIndex, $kinerjaIndex) { unset($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]); $this->visualNodes[$nodeIndex]['kinerja_items'] = array_values($this->visualNodes[$nodeIndex]['kinerja_items']); if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true); }
    public function addIndikatorItem($nodeIndex, $kinerjaIndex) { $this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'][] = ['nama' => '', 'nilai' => '', 'satuan' => '']; if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true); }
    public function removeIndikatorItem($nodeIndex, $kinerjaIndex, $indikatorIndex) { unset($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'][$indikatorIndex]); $this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'] = array_values($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators']); if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true); }


    // ==========================================
    // 9. LOGIC DB LAMA (LEGACY - TABEL BAWAH)
    // ==========================================
    
    private function getFlatTree() { $allNodes = ModelPohon::with(['tujuan', 'indikators'])->orderBy('created_at', 'asc')->get(); $roots = $allNodes->whereNull('parent_id'); $flatList = collect([]); foreach ($roots as $root) { $this->formatTree($root, $allNodes, $flatList, 0); } return $flatList; }
    private function formatTree($node, $allNodes, &$list, $depth) { $node->depth = $depth; $list->push($node); $children = $allNodes->where('parent_id', $node->id); foreach ($children as $child) { $this->formatTree($child, $allNodes, $list, $depth + 1); } }

    private function resetForm() { $this->reset(['tujuan_id', 'nama_pohon', 'parent_id', 'pohon_id', 'isChild', 'isEditMode', 'indikator_input', 'indikator_list']); $this->resetValidation(); }
    public function openModal() { $this->resetForm(); $this->isOpen = true; }
    public function addChild($parentId) { $this->resetForm(); $this->parent_id = $parentId; $this->isChild = true; $parent = ModelPohon::find($parentId); $this->tujuan_id = $parent ? $parent->tujuan_id : null; $this->isOpen = true; }
    public function edit($id) { $this->resetForm(); $pohon = ModelPohon::find($id); if ($pohon) { $this->pohon_id = $id; $this->tujuan_id = $pohon->tujuan_id; $this->nama_pohon = $pohon->nama_pohon; $this->parent_id = $pohon->parent_id; $this->isChild = $pohon->parent_id ? true : false; $this->isEditMode = true; $this->isOpen = true; } }
    public function closeModal() { $this->isOpen = false; $this->isOpenIndikator = false; $this->resetValidation(); }
    public function store() { $rules = ['nama_pohon' => 'required']; if (!$this->isChild && !$this->parent_id) { $rules['tujuan_id'] = 'required'; } $this->validate($rules); if ($this->isEditMode) { ModelPohon::find($this->pohon_id)->update(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon]); } else { ModelPohon::create(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon, 'parent_id' => $this->parent_id]); } $this->closeModal(); session()->flash('message', 'Data disimpan.'); }
    public function delete($id) { $pohon = ModelPohon::find($id); if($pohon) { $pohon->delete(); session()->flash('message', 'Dihapus.'); } }

    // --- MANAGE INDIKATOR LEGACY (UPDATED: NO NILAI/SATUAN) ---
    
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
        $this->indikator_list[] = ['id' => 'temp_' . uniqid(), 'nama' => $this->indikator_input]; 
        $this->reset(['indikator_input']); 
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
                'target' => 0, // Default Dummy
                'satuan' => '-' // Default Dummy
            ]); 
        } 
        $this->closeModal(); 
        session()->flash('message', 'Indikator disimpan.'); 
    }
}