<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon;      // Model Lama (Data Tabel Bawah)
use App\Models\VisualisasiRenstra;              // Model Baru (Data Visualisasi Canvas)
use App\Models\IndikatorPohonKinerja;
use App\Models\Jabatan;                         // Model Jabatan (Untuk Dropdown)

class CascadingRenstra extends Component
{
    use WithPagination;

    // ==========================================
    // 1. STATES & PROPERTIES
    // ==========================================

    // States UI
    public $isOpen = false;
    public $isOpenIndikator = false;
    public $isChild = false;
    public $isEditMode = false;

    // Properties DB Lama (Untuk CRUD Tabel Bawah)
    public $pohon_id, $tujuan_id, $nama_pohon, $parent_id;
    public $indikator_input, $indikator_nilai, $indikator_satuan, $indikator_list = [];

    // Properties Visualisasi (Canvas)
    public $visualNodes = [];

    // ==========================================
    // 2. MOUNT & RENDER
    // ==========================================

    public function mount()
    {
        $this->loadVisualData();
    }

    public function render()
    {
        // A. Data untuk Visualisasi (Canvas)
        $manualTree = $this->buildVisualTreeStructure();

        // B. Data untuk Dropdown Jabatan (Diurutkan agar rapi)
        // Kita urutkan by Level dulu (Atasan ke Bawahan), baru by ID
        $listJabatans = Jabatan::orderBy('level', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

        // C. Data untuk Tabel Database Lama (Bagian Bawah Halaman)
        $treeData = $this->getFlatTree(); 
        $masterPohons = ModelPohon::with('tujuan')->orderBy('id', 'asc')->get();

        return view('livewire.cascading-renstra', [
            'manualTree' => $manualTree,
            'listJabatans' => $listJabatans, // Dikirim ke View
            'pohons' => $treeData, 
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'opsiPohon' => $masterPohons,
        ]);
    }

    // ==========================================
    // 3. LOGIC LOAD DATA VISUALISASI
    // ==========================================

    public function loadVisualData()
    {
        // Ambil Root Nodes dari Tabel Visualisasi
        $dbRoots = VisualisasiRenstra::with('children')
                    ->whereNull('parent_id')
                    ->orderBy('id', 'asc')
                    ->get();
        
        $this->visualNodes = [];

        if ($dbRoots->count() > 0) {
            foreach ($dbRoots as $root) {
                $this->visualNodes[] = $this->formatNodeFromDb($root);
            }
        } else {
            // Jika kosong, inisialisasi node default
            $this->addManualRoot();
        }
    }

    // Helper: Konversi Data DB -> Format Array Visualisasi
    private function formatNodeFromDb($dbNode)
    {
        // Load children secara rekursif
        $children = VisualisasiRenstra::where('parent_id', $dbNode->id)->get();
        $formattedChildren = [];
        foreach($children as $child) {
            $formattedChildren[] = $this->formatNodeFromDb($child);
        }

        // Pastikan format kinerja items aman (array)
        $items = $dbNode->content_data ?? [];
        if(empty($items)) {
            $items = [['kinerja_utama' => '', 'indikators' => []]];
        }

        return [
            'id' => $dbNode->id,
            'parent_id' => $dbNode->parent_id,
            'jabatan' => $dbNode->jabatan,
            'is_locked' => $dbNode->is_locked,
            'kinerja_items' => $items,
            'children' => collect($formattedChildren)
        ];
    }

    // ==========================================
    // 4. LOGIC SIMPAN & UPDATE VISUALISASI
    // ==========================================

    public function saveNodeData($nodeIndex)
    {
        $data = $this->visualNodes[$nodeIndex];
        
        // Tentukan Parent ID (Null jika Root, Numeric jika Child)
        $parentIdToSave = ($data['parent_id'] && is_numeric($data['parent_id'])) ? $data['parent_id'] : null;

        // Cek apakah ini Update atau Create
        $node = is_numeric($data['id']) ? VisualisasiRenstra::find($data['id']) : null;

        if($node) {
            // Update Existing Node
            $node->update([
                'jabatan' => $data['jabatan'],
                'content_data' => $data['kinerja_items'], // Otomatis jadi JSON krn $casts di Model
                'is_locked' => true
            ]);
        } else {
            // Create New Node
            $newNode = VisualisasiRenstra::create([
                'parent_id' => $parentIdToSave,
                'jabatan' => $data['jabatan'],
                'content_data' => $data['kinerja_items'],
                'is_locked' => true
            ]);
            
            // Update ID di array visual dengan ID baru dari DB
            $this->visualNodes[$nodeIndex]['id'] = $newNode->id;
            $this->visualNodes[$nodeIndex]['is_locked'] = true;
        }

        session()->flash('message', 'Data Visualisasi berhasil disimpan!');
    }

    public function lockNode($nodeIndex) 
    { 
        $this->saveNodeData($nodeIndex); 
    }

    public function unlockNode($nodeIndex) 
    { 
        $this->visualNodes[$nodeIndex]['is_locked'] = false; 
    }

    // ==========================================
    // 5. CRUD ARRAY MANIPULATION (VISUAL TREE)
    // ==========================================

    // Helper: Mengubah Flat Array menjadi Tree Structure untuk View
    private function buildVisualTreeStructure()
    {
        $nodes = collect($this->visualNodes)->map(function($item, $key) {
            $item['original_index'] = $key;
            return (object) $item;
        });

        $nodesDict = $nodes->keyBy('id');
        
        // Inisialisasi collection children untuk setiap node
        foreach($nodes as $node) {
            $node->children = collect([]);
        }

        $tree = collect([]);
        
        // Logic penyusunan Tree
        foreach($nodes as $node) {
            if($node->parent_id && isset($nodesDict[$node->parent_id])) {
                $nodesDict[$node->parent_id]->children->push($node);
            } else {
                $tree->push($node);
            }
        }
        
        return $tree;
    }

    public function addManualRoot()
    {
        $this->visualNodes[] = [
            'id' => 'temp_' . uniqid(), 
            'parent_id' => null, 
            'jabatan' => '', 
            'is_locked' => false,
            'kinerja_items' => [['kinerja_utama' => '', 'indikators' => []]]
        ];
    }

    public function addManualChild($parentId)
    {
        if(!is_numeric($parentId)) {
             session()->flash('error', 'Simpan Parent terlebih dahulu!');
             return;
        }

        $this->visualNodes[] = [
            'id' => 'temp_' . uniqid(), 
            'parent_id' => $parentId, 
            'jabatan' => '', 
            'is_locked' => false,
            'kinerja_items' => [['kinerja_utama' => '', 'indikators' => []]]
        ];
    }

    public function deleteManualNode($id)
    {
        if(is_numeric($id)) {
            VisualisasiRenstra::destroy($id);
        }
        $this->loadVisualData(); // Reload agar bersih
    }

    // --- MANAJEMEN ITEM TABEL (KINERJA & INDIKATOR) ---

    public function addKinerjaItem($nodeIndex) 
    { 
        $this->visualNodes[$nodeIndex]['kinerja_items'][] = ['kinerja_utama' => '', 'indikators' => []]; 
    }

    public function removeKinerjaItem($nodeIndex, $kinerjaIndex) 
    { 
        unset($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]); 
        $this->visualNodes[$nodeIndex]['kinerja_items'] = array_values($this->visualNodes[$nodeIndex]['kinerja_items']); 
        
        if(is_numeric($this->visualNodes[$nodeIndex]['id'])) { 
            $this->saveNodeData($nodeIndex); // Autosave
        } 
    }

    public function addIndikatorItem($nodeIndex, $kinerjaIndex) 
    { 
        $this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'][] = ['nama' => '', 'nilai' => '', 'satuan' => '']; 
    }

    public function removeIndikatorItem($nodeIndex, $kinerjaIndex, $indikatorIndex) 
    { 
        unset($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'][$indikatorIndex]); 
        $this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'] = array_values($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators']); 
        
        if(is_numeric($this->visualNodes[$nodeIndex]['id'])) { 
            $this->saveNodeData($nodeIndex); // Autosave
        } 
    }

    // ==========================================
    // 6. LOGIC CRUD DATABASE LAMA (LEGACY)
    // ==========================================
    // Bagian ini menangani Tabel "Data Cascading Renstra" di bagian bawah halaman
    
    private function getFlatTree() { 
        $allNodes = ModelPohon::with(['tujuan', 'indikators', 'children.indikators', 'children.children.indikators'])
                    ->orderBy('created_at', 'asc')->get(); 
        $roots = $allNodes->whereNull('parent_id'); 
        $flatList = collect([]); 
        foreach ($roots as $root) { $this->formatTree($root, $allNodes, $flatList, 0); } 
        return $flatList; 
    }

    private function formatTree($node, $allNodes, &$list, $depth) { 
        $node->depth = $depth; 
        $list->push($node); 
        $children = $allNodes->where('parent_id', $node->id); 
        foreach ($children as $child) { $this->formatTree($child, $allNodes, $list, $depth + 1); } 
    }

    // Form Handlers Legacy
    private function resetForm() { 
        $this->reset(['tujuan_id', 'nama_pohon', 'parent_id', 'pohon_id', 'isChild', 'isEditMode', 'indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list']); 
        $this->resetValidation(); 
    }
    
    public function openModal() { $this->resetForm(); $this->isOpen = true; }
    
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
        $this->resetValidation(); 
    }
    
    public function store() { 
        $rules = ['nama_pohon' => 'required']; 
        if (!$this->isChild && !$this->parent_id) { $rules['tujuan_id'] = 'required'; } 
        $this->validate($rules); 
        
        if ($this->isEditMode) { 
            ModelPohon::find($this->pohon_id)->update(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon]); 
        } else { 
            ModelPohon::create(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon, 'parent_id' => $this->parent_id]); 
        } 
        $this->closeModal(); 
        session()->flash('message', 'Data disimpan.'); 
    }
    
    public function delete($id) { 
        $pohon = ModelPohon::find($id); 
        if($pohon) { 
            $pohon->delete(); 
            session()->flash('message', 'Dihapus.'); 
        } 
    }

    // Indikator Handlers Legacy
    public function openIndikator($pohonId) { 
        $this->pohon_id = $pohonId; 
        $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list']); 
        $existing = IndikatorPohonKinerja::where('pohon_kinerja_id', $pohonId)->get(); 
        foreach($existing as $ind) { 
            $this->indikator_list[] = ['id' => $ind->id, 'nama' => $ind->nama_indikator, 'nilai' => $ind->target ?? 0, 'satuan' => $ind->satuan ?? '-']; 
        } 
        $this->isOpenIndikator = true; 
    }
    
    public function addIndikatorToList() { 
        $this->validate(['indikator_input' => 'required', 'indikator_nilai' => 'required', 'indikator_satuan' => 'required']); 
        $this->indikator_list[] = ['id' => 'temp_' . uniqid(), 'nama' => $this->indikator_input, 'nilai' => $this->indikator_nilai, 'satuan' => $this->indikator_satuan]; 
        $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan']); 
    }
    
    public function removeIndikatorFromList($index) { 
        unset($this->indikator_list[$index]); 
        $this->indikator_list = array_values($this->indikator_list); 
    }
    
    public function saveIndikators() { 
        IndikatorPohonKinerja::where('pohon_kinerja_id', $this->pohon_id)->delete(); 
        foreach($this->indikator_list as $ind) { 
            IndikatorPohonKinerja::create(['pohon_kinerja_id' => $this->pohon_id, 'nama_indikator' => $ind['nama'], 'target' => $ind['nilai'], 'satuan' => $ind['satuan']]); 
        } 
        $this->closeModal(); 
        session()->flash('message', 'Indikator disimpan.'); 
    }
}