<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon;
use App\Models\VisualisasiRenstra;
use App\Models\IndikatorPohonKinerja;
use App\Models\Jabatan;

class CascadingRenstra extends Component
{
    use WithPagination;

    // --- STATES ---
    public $isOpen = false;
    public $isOpenIndikator = false;
    public $isChild = false;
    public $isEditMode = false;

    // --- DB PROPERTIES (LAMA) ---
    public $pohon_id, $tujuan_id, $nama_pohon, $parent_id;
    public $indikator_input, $indikator_nilai, $indikator_satuan, $indikator_list = [];

    // --- DATA VISUALISASI ---
    // Array ini sekarang akan berisi SEMUA node secara datar (Flat List)
    public $visualNodes = [];

    public function mount()
    {
        $this->loadVisualData();
    }

    public function render()
    {
        // 1. Build Tree Structure untuk View
        // Mengubah Flat List $visualNodes menjadi Struktur Pohon
        $manualTree = $this->buildVisualTreeStructure();

        // 2. Data Dropdown Jabatan
        $listJabatans = Jabatan::orderBy('level', 'asc')->orderBy('id', 'asc')->get();

        // 3. Data Tabel Bawah (Legacy)
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
    // LOGIC LOAD DATA (PERBAIKAN UTAMA DISINI)
    // ==========================================

    public function loadVisualData()
    {
        // UBAH LOGIC: Ambil SEMUA data (bukan cuma root) agar jadi Flat List
        // Ini memastikan node anak (ID 21, 22, dst) ikut termuat ke visualNodes
        $allNodes = VisualisasiRenstra::orderBy('id', 'asc')->get();
        
        $this->visualNodes = [];

        if ($allNodes->count() > 0) {
            foreach ($allNodes as $node) {
                $this->visualNodes[] = $this->formatNodeFlat($node);
            }
        } else {
            // Jika kosong, baru buat root manual
            $this->addManualRoot();
        }
    }

    // Helper Format Flat (Tanpa Rekursi Children)
    private function formatNodeFlat($dbNode)
    {
        $items = $dbNode->content_data;
        
        // Safety check untuk JSON decoding
        if(is_string($items)) {
            $items = json_decode($items, true);
        }
        if(empty($items) || !is_array($items)) {
            $items = [['kinerja_utama' => '', 'indikators' => []]];
        }

        return [
            'id' => $dbNode->id,
            'parent_id' => $dbNode->parent_id,
            'jabatan' => $dbNode->jabatan,
            'is_locked' => $dbNode->is_locked,
            'kinerja_items' => $items,
            // Note: Kita TIDAK set 'children' disini. 
            // 'children' akan dibuat otomatis oleh buildVisualTreeStructure di render()
        ];
    }

    // ==========================================
    // REAL-TIME AUTO SAVE
    // ==========================================
    
    public function updated($property)
    {
        if (str_starts_with($property, 'visualNodes.')) {
            $parts = explode('.', $property);
            $index = $parts[1] ?? null;

            if ($index !== null && is_numeric($index)) {
                // Autosave hanya jika node sudah ada di DB (bukan temp)
                if (isset($this->visualNodes[$index]['id']) && is_numeric($this->visualNodes[$index]['id'])) {
                    $this->saveNodeData($index, true); // true = silent mode (no flash msg)
                }
            }
        }
    }

    // ==========================================
    // LOGIC SIMPAN & UPDATE
    // ==========================================

    public function saveNodeData($nodeIndex, $silent = false)
    {
        $data = $this->visualNodes[$nodeIndex];
        $parentIdToSave = ($data['parent_id'] && is_numeric($data['parent_id'])) ? $data['parent_id'] : null;

        $node = is_numeric($data['id']) ? VisualisasiRenstra::find($data['id']) : null;

        if($node) {
            // UPDATE
            $node->update([
                'jabatan' => $data['jabatan'],
                'content_data' => $data['kinerja_items'],
                'is_locked' => true
            ]);
        } else {
            // CREATE NEW
            $newNode = VisualisasiRenstra::create([
                'parent_id' => $parentIdToSave,
                'jabatan' => $data['jabatan'],
                'content_data' => $data['kinerja_items'],
                'is_locked' => true
            ]);
            
            // Simpan ID lama (temp_xxx)
            $oldId = $this->visualNodes[$nodeIndex]['id'];

            // Update Array Visual dengan ID Baru dari DB
            $this->visualNodes[$nodeIndex]['id'] = $newNode->id;
            $this->visualNodes[$nodeIndex]['is_locked'] = true;

            // PENTING: Update Parent ID anak-anaknya yang masih pakai ID temp
            // Agar rantai pohon tidak putus setelah parent disimpan
            foreach($this->visualNodes as $key => $vNode) {
                if($vNode['parent_id'] === $oldId) {
                    $this->visualNodes[$key]['parent_id'] = $newNode->id;
                    // Kita bisa autosave anak disini jika mau, tapi update array cukup utk visual
                }
            }
        }

        if(!$silent) session()->flash('message', 'Data tersimpan!');
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
    // LOGIC PENYUSUN POHON (TREE BUILDER)
    // ==========================================

    private function buildVisualTreeStructure()
    {
        // 1. Mapping ke Object dan simpan Index Asli
        // Index asli penting agar wire:model="visualNodes.INDEX..." tetap akurat
        $nodes = collect($this->visualNodes)->map(function($item, $key) {
            $item['original_index'] = $key;
            return (object) $item;
        });

        // 2. Dictionary Key by ID untuk akses cepat
        $nodesDict = $nodes->keyBy('id');
        
        // 3. Siapkan container children
        foreach($nodes as $node) {
            $node->children = collect([]);
        }

        $tree = collect([]);
        
        // 4. Susun Hirarki
        foreach($nodes as $node) {
            if($node->parent_id && isset($nodesDict[$node->parent_id])) {
                // Jika punya parent valid, masukkan ke children parent tersebut
                $nodesDict[$node->parent_id]->children->push($node);
            } else {
                // Jika tidak punya parent (atau parent tidak ketemu), anggap sebagai Root
                $tree->push($node);
            }
        }
        
        return $tree;
    }

    // ==========================================
    // CRUD ARRAY MANIPULATION
    // ==========================================

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
        // Pastikan Parent bukan temp (harus saved) agar relasi aman
        if(!is_numeric($parentId)) {
             session()->flash('error', 'Mohon Simpan (Lock) Parent terlebih dahulu sebelum menambah cabang!');
             return;
        }

        $this->visualNodes[] = [
            'id' => 'temp_' . uniqid(), 
            'parent_id' => $parentId, 
            'jabatan' => '', 
            'is_locked' => false,
            'kinerja_items' => [['kinerja_utama' => '', 'indikators' => []]]
        ];
        
        // Tidak perlu reload visual data, karena kita push ke array. 
        // View akan otomatis re-render via buildVisualTreeStructure
    }

    public function deleteManualNode($id)
    {
        if(is_numeric($id)) {
            // Hapus dari DB (Cascade delete anak-anak di DB)
            VisualisasiRenstra::destroy($id);
        }

        // Hapus dari Array Visual (Manual Recursion untuk membersihkan Array)
        // Cara paling aman & bersih: Reload dari DB
        // Tapi jika node itu temp, kita harus hapus manual dari array
        
        if(is_numeric($id)) {
            $this->loadVisualData(); // Reload clean dari DB
        } else {
            // Hapus temp node dari array
            // Cari index
            $indexToDelete = null;
            foreach($this->visualNodes as $key => $node) {
                if($node['id'] === $id) {
                    $indexToDelete = $key;
                    break;
                }
            }
            if($indexToDelete !== null) {
                unset($this->visualNodes[$indexToDelete]);
                $this->visualNodes = array_values($this->visualNodes);
            }
        }
    }

    // --- ITEM HANDLERS (KINERJA & INDIKATOR) ---
    // Ditambahkan autosave check

    public function addKinerjaItem($nodeIndex) 
    { 
        $this->visualNodes[$nodeIndex]['kinerja_items'][] = ['kinerja_utama' => '', 'indikators' => []]; 
        if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true);
    }

    public function removeKinerjaItem($nodeIndex, $kinerjaIndex) 
    { 
        unset($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]); 
        $this->visualNodes[$nodeIndex]['kinerja_items'] = array_values($this->visualNodes[$nodeIndex]['kinerja_items']); 
        if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true);
    }

    public function addIndikatorItem($nodeIndex, $kinerjaIndex) 
    { 
        $this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'][] = ['nama' => '', 'nilai' => '', 'satuan' => '']; 
        if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true);
    }

    public function removeIndikatorItem($nodeIndex, $kinerjaIndex, $indikatorIndex) 
    { 
        unset($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'][$indikatorIndex]); 
        $this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators'] = array_values($this->visualNodes[$nodeIndex]['kinerja_items'][$kinerjaIndex]['indikators']); 
        if(is_numeric($this->visualNodes[$nodeIndex]['id'])) $this->saveNodeData($nodeIndex, true);
    }

    // ==========================================
    // FUNGSI DB LAMA (LEGACY)
    // ==========================================
    
    private function getFlatTree() { 
        $allNodes = ModelPohon::with(['tujuan', 'indikators', 'children.indikators', 'children.children.indikators'])->orderBy('created_at', 'asc')->get(); 
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

    private function resetForm() { $this->reset(['tujuan_id', 'nama_pohon', 'parent_id', 'pohon_id', 'isChild', 'isEditMode', 'indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list']); $this->resetValidation(); }
    public function openModal() { $this->resetForm(); $this->isOpen = true; }
    public function addChild($parentId) { $this->resetForm(); $this->parent_id = $parentId; $this->isChild = true; $parent = ModelPohon::find($parentId); $this->tujuan_id = $parent ? $parent->tujuan_id : null; $this->isOpen = true; }
    public function edit($id) { $this->resetForm(); $pohon = ModelPohon::find($id); if ($pohon) { $this->pohon_id = $id; $this->tujuan_id = $pohon->tujuan_id; $this->nama_pohon = $pohon->nama_pohon; $this->parent_id = $pohon->parent_id; $this->isChild = $pohon->parent_id ? true : false; $this->isEditMode = true; $this->isOpen = true; } }
    public function closeModal() { $this->isOpen = false; $this->isOpenIndikator = false; $this->resetValidation(); }
    public function store() { $rules = ['nama_pohon' => 'required']; if (!$this->isChild && !$this->parent_id) { $rules['tujuan_id'] = 'required'; } $this->validate($rules); if ($this->isEditMode) { ModelPohon::find($this->pohon_id)->update(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon]); } else { ModelPohon::create(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon, 'parent_id' => $this->parent_id]); } $this->closeModal(); session()->flash('message', 'Data disimpan.'); }
    public function delete($id) { $pohon = ModelPohon::find($id); if($pohon) { $pohon->delete(); session()->flash('message', 'Dihapus.'); } }
    public function openIndikator($pohonId) { $this->pohon_id = $pohonId; $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list']); $existing = IndikatorPohonKinerja::where('pohon_kinerja_id', $pohonId)->get(); foreach($existing as $ind) { $this->indikator_list[] = ['id' => $ind->id, 'nama' => $ind->nama_indikator, 'nilai' => $ind->target ?? 0, 'satuan' => $ind->satuan ?? '-']; } $this->isOpenIndikator = true; }
    public function addIndikatorToList() { $this->validate(['indikator_input' => 'required', 'indikator_nilai' => 'required', 'indikator_satuan' => 'required']); $this->indikator_list[] = ['id' => 'temp_' . uniqid(), 'nama' => $this->indikator_input, 'nilai' => $this->indikator_nilai, 'satuan' => $this->indikator_satuan]; $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan']); }
    public function removeIndikatorFromList($index) { unset($this->indikator_list[$index]); $this->indikator_list = array_values($this->indikator_list); }
    public function saveIndikators() { IndikatorPohonKinerja::where('pohon_kinerja_id', $this->pohon_id)->delete(); foreach($this->indikator_list as $ind) { IndikatorPohonKinerja::create(['pohon_kinerja_id' => $this->pohon_id, 'nama_indikator' => $ind['nama'], 'target' => $ind['nilai'], 'satuan' => $ind['satuan']]); } $this->closeModal(); session()->flash('message', 'Indikator disimpan.'); }
}