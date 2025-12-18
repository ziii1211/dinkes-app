<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tujuan;
use App\Models\PohonKinerja as ModelPohon; 
use App\Models\IndikatorPohonKinerja;

class CascadingRenstra extends Component
{
    use WithPagination;

    // --- STATES ---
    public $isOpen = false; 
    public $isOpenIndikator = false; 
    
    // STATE BARU: Untuk Modal Input Manual
    public $isOpenManualInput = false;
    public $activeManualNodeId = null;

    public $isChild = false;
    public $isEditMode = false;

    // --- FORM PROPERTIES DB ---
    public $pohon_id; 
    public $tujuan_id; 
    public $nama_pohon;
    public $parent_id;

    // Properties Indikator DB
    public $indikator_input; 
    public $indikator_nilai;
    public $indikator_satuan;
    public $indikator_list = []; 

    // --- FORM PROPERTIES MANUAL (BARU) ---
    public $manual_kinerja;
    public $manual_indikator;
    public $manual_target_nilai;
    public $manual_target_satuan;

    // DATA VISUALISASI MANUAL
    // Struktur array kita perbarui untuk menampung field detail
    public $visualNodes = [
        [
            'id' => 'root_default',
            'parent_id' => null,
            'kinerja_utama' => 'Sasaran Strategis (Contoh)',
            'indikator' => 'Indikator Kinerja Utama',
            'target_nilai' => '100',
            'target_satuan' => '%',
        ]
    ];

    public function render()
    {
        $treeData = $this->getFlatTree();
        $masterPohons = ModelPohon::with('tujuan')->orderBy('id', 'asc')->get();
        
        // Build Tree untuk Visualisasi Manual
        $manualTree = $this->buildManualTree();

        return view('livewire.cascading-renstra', [
            'pohons' => $treeData, 
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'opsiPohon' => $masterPohons,
            'manualTree' => $manualTree, 
        ]);
    }

    // --- LOGIC VISUALISASI MANUAL ---
    
    private function buildManualTree()
    {
        $nodes = collect($this->visualNodes);
        $roots = $nodes->where('parent_id', null);

        $tree = [];
        foreach($roots as $root) {
            $tree[] = $this->formatManualNode($root, $nodes);
        }
        return collect($tree);
    }

    private function formatManualNode($node, $allNodes)
    {
        $children = $allNodes->where('parent_id', $node['id'])->values();
        
        $formattedChildren = [];
        foreach($children as $child) {
            $formattedChildren[] = $this->formatManualNode($child, $allNodes);
        }

        // Mapping array ke object untuk View
        return (object) [
            'id' => $node['id'],
            'kinerja_utama' => $node['kinerja_utama'] ?? '',
            'indikator' => $node['indikator'] ?? '',
            'target_nilai' => $node['target_nilai'] ?? '',
            'target_satuan' => $node['target_satuan'] ?? '',
            'children' => collect($formattedChildren)
        ];
    }

    public function addManualRoot()
    {
        $this->visualNodes[] = [
            'id' => uniqid('node_'),
            'parent_id' => null,
            'kinerja_utama' => 'Kinerja Baru',
            'indikator' => '-',
            'target_nilai' => '0',
            'target_satuan' => '-',
        ];
    }

    public function addManualChild($parentId)
    {
        $this->visualNodes[] = [
            'id' => uniqid('node_'),
            'parent_id' => $parentId,
            'kinerja_utama' => 'Sub Kinerja Baru',
            'indikator' => '-',
            'target_nilai' => '0',
            'target_satuan' => '-',
        ];
    }

    public function deleteManualNode($id)
    {
        // Recursive delete logic sederhana
        $idsToDelete = [$id];
        $currentLayer = [$id];
        
        while(count($currentLayer) > 0) {
            $nextLayer = [];
            foreach($this->visualNodes as $node) {
                if(in_array($node['parent_id'], $currentLayer)) {
                    $nextLayer[] = $node['id'];
                    $idsToDelete[] = $node['id'];
                }
            }
            $currentLayer = $nextLayer;
        }

        $this->visualNodes = collect($this->visualNodes)
            ->whereNotIn('id', $idsToDelete)
            ->values()
            ->toArray();
    }

    // --- LOGIC FORM INPUT MANUAL ---

    public function openManualInput($id)
    {
        // Cari data node berdasarkan ID
        $node = collect($this->visualNodes)->firstWhere('id', $id);
        
        if($node) {
            $this->activeManualNodeId = $id;
            $this->manual_kinerja = $node['kinerja_utama'] ?? '';
            $this->manual_indikator = $node['indikator'] ?? '';
            $this->manual_target_nilai = $node['target_nilai'] ?? '';
            $this->manual_target_satuan = $node['target_satuan'] ?? '';
            
            $this->isOpenManualInput = true;
        }
    }

    public function updateManualNode()
    {
        $this->validate([
            'manual_kinerja' => 'required',
        ]);

        // Update data di array
        foreach($this->visualNodes as $key => $node) {
            if($node['id'] === $this->activeManualNodeId) {
                $this->visualNodes[$key]['kinerja_utama'] = $this->manual_kinerja;
                $this->visualNodes[$key]['indikator'] = $this->manual_indikator;
                $this->visualNodes[$key]['target_nilai'] = $this->manual_target_nilai;
                $this->visualNodes[$key]['target_satuan'] = $this->manual_target_satuan;
                break;
            }
        }

        $this->closeManualModal();
    }

    public function closeManualModal()
    {
        $this->isOpenManualInput = false;
        $this->reset(['manual_kinerja', 'manual_indikator', 'manual_target_nilai', 'manual_target_satuan', 'activeManualNodeId']);
    }

    // ... (SISA KODE LAMA ANDA UNTUK DB SEPERTI getFlatTree, store, delete dll TETAP ADA DI BAWAH SINI) ...
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
    public function store() { 
        $rules = ['nama_pohon' => 'required'];
        if (!$this->isChild && !$this->parent_id) { $rules['tujuan_id'] = 'required'; } 
        $this->validate($rules);
        if ($this->isEditMode) { ModelPohon::find($this->pohon_id)->update(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon]); } else { ModelPohon::create(['tujuan_id' => $this->tujuan_id, 'nama_pohon' => $this->nama_pohon, 'parent_id' => $this->parent_id]); }
        $this->closeModal(); session()->flash('message', 'Data disimpan.');
    }
    public function delete($id) { $pohon = ModelPohon::find($id); if($pohon) { $pohon->delete(); session()->flash('message', 'Dihapus.'); } }
    public function openIndikator($pohonId) { $this->pohon_id = $pohonId; $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan', 'indikator_list']); $existing = IndikatorPohonKinerja::where('pohon_kinerja_id', $pohonId)->get(); foreach($existing as $ind) { $this->indikator_list[] = ['id' => $ind->id, 'nama' => $ind->nama_indikator, 'nilai' => $ind->target ?? 0, 'satuan' => $ind->satuan ?? '-']; } $this->isOpenIndikator = true; }
    public function addIndikatorToList() { $this->validate(['indikator_input' => 'required', 'indikator_nilai' => 'required', 'indikator_satuan' => 'required']); $this->indikator_list[] = ['id' => 'temp_' . uniqid(), 'nama' => $this->indikator_input, 'nilai' => $this->indikator_nilai, 'satuan' => $this->indikator_satuan]; $this->reset(['indikator_input', 'indikator_nilai', 'indikator_satuan']); }
    public function removeIndikatorFromList($index) { unset($this->indikator_list[$index]); $this->indikator_list = array_values($this->indikator_list); }
    public function saveIndikators() { IndikatorPohonKinerja::where('pohon_kinerja_id', $this->pohon_id)->delete(); foreach($this->indikator_list as $ind) { IndikatorPohonKinerja::create(['pohon_kinerja_id' => $this->pohon_id, 'nama_indikator' => $ind['nama'], 'target' => $ind['nilai'], 'satuan' => $ind['satuan']]); } $this->closeModal(); session()->flash('message', 'Indikator disimpan.'); }
}