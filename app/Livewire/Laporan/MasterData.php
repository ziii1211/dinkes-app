<?php

namespace App\Livewire\Laporan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\IndikatorSubKegiatan;
use Livewire\Attributes\Title;

class MasterData extends Component
{
    use WithPagination;

    // --- PROPERTI UI ---
    public $isOpen = false;
    public $isOpenIndikator = false;
    public $isEditMode = false;
    
    // Properti untuk Show Entries (Default 10)
    public $perPage = 10; 
    
    // --- PROPERTI HAPUS ---
    public $deleteTarget = ''; 
    public $deleteId = null;

    // --- PROPERTI FORM UTAMA (Program/Kegiatan/Sub) ---
    public $formType = 'program'; // Opsi: 'program', 'kegiatan', 'sub_kegiatan'
    public $parentId = null; 
    public $dataId = null;   
    public $kode, $nama;

    // --- PROPERTI MODAL INDIKATOR ---
    public $selectedSubKegiatan = null;
    public $indikators = [];
    public $indikatorId = null;
    public $subOutput, $satuan;

    // Listener Event
    protected $listeners = ['deleteConfirmed' => 'delete'];

    #[Title('Master Data Laporan')]
    public function render()
    {
        // Query Data: Program -> Kegiatan -> Sub Kegiatan
        // Diurutkan berdasarkan Kode (A-Z) lalu ID (Input Lama ke Baru)
        $programs = Program::with(['kegiatans' => function($q) {
            $q->orderBy('kode', 'asc')
              ->orderBy('id', 'asc')
              ->with(['subKegiatans' => function($sub) {
                  $sub->orderBy('kode', 'asc')
                      ->orderBy('id', 'asc');
              }]);
        }])
        ->orderBy('kode', 'asc')
        ->orderBy('id', 'asc')
        // Gunakan variable $this->perPage agar dinamis
        ->paginate($this->perPage); 

        return view('livewire.laporan.master-data', [
            'programs' => $programs
        ]);
    }

    // --- HELPER RESET & CLOSE ---
    
    private function resetInput()
    {
        // Reset Form Utama
        $this->kode = null;
        $this->nama = null;
        $this->dataId = null;
        $this->parentId = null;
        $this->isEditMode = false;
        
        // Reset Form Indikator
        $this->indikatorId = null;
        $this->subOutput = null;
        $this->satuan = null;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInput();
    }

    public function closeIndikatorModal()
    {
        $this->isOpenIndikator = false;
        $this->selectedSubKegiatan = null;
        $this->resetInput();
    }

    // --- LOGIKA FORM UTAMA (CREATE & EDIT) ---

    // 1. Program
    public function createProgram() { $this->setupForm('program'); }
    public function editProgram($id) { $this->loadForm('program', $id); }
    
    // 2. Kegiatan
    public function createKegiatan($parentId) { $this->setupForm('kegiatan', $parentId); }
    public function editKegiatan($id) { $this->loadForm('kegiatan', $id); }

    // 3. Sub Kegiatan
    public function createSubKegiatan($parentId) { $this->setupForm('sub_kegiatan', $parentId); }
    public function editSubKegiatan($id) { $this->loadForm('sub_kegiatan', $id); }

    // Helper Setup Form Baru
    private function setupForm($type, $parent = null)
    {
        $this->resetInput();
        $this->formType = $type;
        $this->parentId = $parent;
        $this->isOpen = true;
    }

    // Helper Load Data untuk Edit
    private function loadForm($type, $id)
    {
        $model = match($type) {
            'program' => Program::class,
            'kegiatan' => Kegiatan::class,
            'sub_kegiatan' => SubKegiatan::class,
        };
        
        $data = $model::findOrFail($id);
        
        $this->resetInput();
        $this->formType = $type;
        $this->dataId = $id;
        $this->kode = $data->kode;
        $this->nama = $data->nama;
        
        // Set Parent ID sesuai tipe
        if ($type == 'kegiatan') $this->parentId = $data->program_id;
        if ($type == 'sub_kegiatan') $this->parentId = $data->kegiatan_id;
        
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    // Simpan Data Utama
    public function store()
    {
        $this->validate([
            'kode' => 'required',
            'nama' => 'required'
        ]);

        $data = ['kode' => $this->kode, 'nama' => $this->nama];

        if ($this->formType == 'program') {
            Program::updateOrCreate(['id' => $this->dataId], $data);
        } elseif ($this->formType == 'kegiatan') {
            $data['program_id'] = $this->parentId;
            Kegiatan::updateOrCreate(['id' => $this->dataId], $data);
        } elseif ($this->formType == 'sub_kegiatan') {
            $data['kegiatan_id'] = $this->parentId;
            SubKegiatan::updateOrCreate(['id' => $this->dataId], $data);
        }

        $this->dispatch('alert', ['type' => 'success', 'title' => 'Berhasil!', 'message' => 'Data berhasil disimpan.']);
        $this->closeModal();
    }

    // --- LOGIKA INDIKATOR KINERJA ---

    public function openIndikator($subKegiatanId)
    {
        $this->resetInput();
        $this->selectedSubKegiatan = SubKegiatan::with('indikators')->findOrFail($subKegiatanId);
        $this->indikators = $this->selectedSubKegiatan->indikators;
        $this->isOpenIndikator = true;
    }

    public function editIndikator($id)
    {
        $indikator = IndikatorSubKegiatan::findOrFail($id);
        $this->indikatorId = $id;
        $this->subOutput = $indikator->keterangan;
        $this->satuan = $indikator->satuan;
    }

    public function saveIndikator()
    {
        $this->validate([
            'subOutput' => 'required',
            'satuan' => 'required',
        ]);

        IndikatorSubKegiatan::updateOrCreate(
            ['id' => $this->indikatorId],
            [
                'sub_kegiatan_id' => $this->selectedSubKegiatan->id,
                'keterangan' => $this->subOutput,
                'satuan' => $this->satuan
            ]
        );

        $this->openIndikator($this->selectedSubKegiatan->id); // Refresh list
        
        // Reset field input kecil
        $this->indikatorId = null;
        $this->subOutput = null;
        $this->satuan = null;

        $this->dispatch('alert', ['type' => 'success', 'title' => 'Berhasil!', 'message' => 'Indikator berhasil disimpan.']);
    }

    public function deleteIndikator($id)
    {
        IndikatorSubKegiatan::find($id)?->delete();
        $this->openIndikator($this->selectedSubKegiatan->id); // Refresh list
    }

    // --- LOGIKA HAPUS GLOBAL ---

    public function confirmDelete($id, $type)
    {
        $this->deleteId = $id;
        $this->deleteTarget = $type;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        // Handle format ID jika dikirim sebagai array oleh SweetAlert
        $idToDelete = is_array($id) ? ($id['id'] ?? array_values($id)[0] ?? null) : $id;

        if (!$idToDelete) return;

        if ($this->deleteTarget == 'program') {
            Program::find($idToDelete)?->delete();
        } elseif ($this->deleteTarget == 'kegiatan') {
            Kegiatan::find($idToDelete)?->delete();
        } elseif ($this->deleteTarget == 'sub_kegiatan') {
            SubKegiatan::find($idToDelete)?->delete();
        }

        $this->dispatch('alert', ['type' => 'success', 'title' => 'Terhapus!', 'message' => 'Data berhasil dihapus.']);
    }
}