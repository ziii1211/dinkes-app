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

    // --- STATES ---
    public $isOpen = false; 
    public $isOpenIndikator = false; 
    public $isOpenCrosscutting = false;
    
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

    // --- PROPERTIES CROSSCUTTING ---
    public $cross_sumber_id;    
    public $cross_skpd_id;      
    public $cross_tujuan_id;    

    public function render()
    {
        // 1. Ambil Struktur Pohon untuk Visualisasi (Induk -> Anak -> Cucu)
        $treeData = $this->getFlatTree();

        // 2. Ambil Data Master untuk Dropdown & Tabel
        // Karena kolom jenis dihapus, kita ambil semua data murni
        $masterPohons = ModelPohon::with('tujuan')
                                ->orderBy('id', 'asc')
                                ->get();

        // 3. Data lain
        $opsiSkpd = SkpdTujuan::orderBy('nama_skpd', 'asc')->get();
        $crosscuttings = CrosscuttingKinerja::with(['pohonSumber', 'skpdTujuan', 'pohonTujuan'])
                                            ->latest()
                                            ->paginate(5);

        return view('livewire.pohon-kinerja', [
            'pohons' => $treeData, // Ini digunakan untuk Tabel & Visualisasi Diagram
            'sasaran_rpjmds' => Tujuan::select('id', 'sasaran_rpjmd')->get(),
            'skpds' => SkpdTujuan::all(), 
            'opsiSkpd' => $opsiSkpd, 
            'opsiPohon' => $masterPohons, 
            'crosscuttings' => $crosscuttings
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
            'cross_sumber_id', 'cross_skpd_id', 'cross_tujuan_id',
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
        $this->isOpenCrosscutting = false;
        $this->resetValidation();
    }

    // --- SIMPAN DATA (Logika Disederhanakan) ---
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
        session()->flash('message', 'Data Pohon Kinerja berhasil disimpan.');
    }

    public function delete($id) { 
        $pohon = ModelPohon::find($id); 
        if($pohon) { 
            $pohon->delete(); 
            session()->flash('message', 'Pohon Kinerja berhasil dihapus.');
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

    // --- CROSSCUTTING ---
    public function openCrosscuttingModal($pohonId = null) { 
        $this->reset(['cross_sumber_id', 'cross_skpd_id', 'cross_tujuan_id']); 
        
        // Jika dibuka dari tombol di diagram, otomatis isi sumbernya
        if($pohonId) {
            $this->cross_sumber_id = $pohonId; 
        }

        $this->isOpenCrosscutting = true; 
    }

    public function storeCrosscutting() {
        $this->validate([
            'cross_sumber_id' => 'required', 
            'cross_skpd_id' => 'required', 
            'cross_tujuan_id' => 'required'
        ]);

        // Simpan hanya ke tabel Crosscutting, tidak perlu buat duplikat node di pohon
        CrosscuttingKinerja::create([
            'pohon_sumber_id' => $this->cross_sumber_id, 
            'skpd_tujuan_id' => $this->cross_skpd_id, 
            'pohon_tujuan_id' => $this->cross_tujuan_id
        ]);
        
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