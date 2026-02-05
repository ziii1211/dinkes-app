<?php

namespace App\Livewire\Laporan;

use Livewire\Component;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan; // Pastikan Model SubKegiatan diimport
use Livewire\Attributes\Title;

class MasterData extends Component
{
    // Properties
    public $isOpen = false;
    public $isEditMode = false;
    public $deleteTarget = ''; 
    public $deleteId = null;

    // Form Fields
    public $formType = 'program'; // 'program', 'kegiatan', 'sub_kegiatan'
    public $parentId = null; 
    public $dataId = null;   
    public $kode, $nama;

    protected $listeners = ['deleteConfirmed' => 'delete'];

    #[Title('Master Data Laporan')]
    public function render()
    {
        // Load Program -> Kegiatan -> SubKegiatan (Nested Eager Loading)
        $programs = Program::with(['kegiatans' => function($q) {
            $q->orderBy('kode', 'asc')->with(['subKegiatans' => function($sub) {
                $sub->orderBy('kode', 'asc');
            }]);
        }])->orderBy('kode', 'asc')->get();

        return view('livewire.laporan.master-data', [
            'programs' => $programs
        ]);
    }

    private function resetInput()
    {
        $this->kode = null;
        $this->nama = null;
        $this->dataId = null;
        $this->parentId = null;
        $this->isEditMode = false;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInput();
    }

    // --- FORM SETUP ---

    // 1. Program
    public function createProgram()
    {
        $this->resetInput();
        $this->formType = 'program';
        $this->isOpen = true;
    }
    public function editProgram($id)
    {
        $data = Program::findOrFail($id);
        $this->setupForm('program', $id, null, $data->kode, $data->nama);
    }

    // 2. Kegiatan
    public function createKegiatan($programId)
    {
        $this->resetInput();
        $this->formType = 'kegiatan';
        $this->parentId = $programId;
        $this->isOpen = true;
    }
    public function editKegiatan($id)
    {
        $data = Kegiatan::findOrFail($id);
        $this->setupForm('kegiatan', $id, $data->program_id, $data->kode, $data->nama);
    }

    // 3. Sub Kegiatan (BARU)
    public function createSubKegiatan($kegiatanId)
    {
        $this->resetInput();
        $this->formType = 'sub_kegiatan';
        $this->parentId = $kegiatanId; // Parent-nya adalah Kegiatan
        $this->isOpen = true;
    }

    public function editSubKegiatan($id)
    {
        $data = SubKegiatan::findOrFail($id);
        $this->setupForm('sub_kegiatan', $id, $data->kegiatan_id, $data->kode, $data->nama);
    }

    // Helper untuk setup form edit
    private function setupForm($type, $id, $parent, $kode, $nama)
    {
        $this->formType = $type;
        $this->dataId = $id;
        $this->parentId = $parent;
        $this->kode = $kode;
        $this->nama = $nama;
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    // --- SIMPAN DATA ---
    public function store()
    {
        $this->validate([
            'kode' => 'required',
            'nama' => 'required',
        ]);

        if ($this->formType == 'program') {
            Program::updateOrCreate(['id' => $this->dataId], ['kode' => $this->kode, 'nama' => $this->nama]);
            $msg = 'Program berhasil disimpan.';
        } elseif ($this->formType == 'kegiatan') {
            Kegiatan::updateOrCreate(
                ['id' => $this->dataId],
                ['program_id' => $this->parentId, 'kode' => $this->kode, 'nama' => $this->nama]
            );
            $msg = 'Kegiatan berhasil disimpan.';
        } elseif ($this->formType == 'sub_kegiatan') {
            SubKegiatan::updateOrCreate(
                ['id' => $this->dataId],
                ['kegiatan_id' => $this->parentId, 'kode' => $this->kode, 'nama' => $this->nama]
            );
            $msg = 'Sub Kegiatan berhasil disimpan.';
        }

        $this->dispatch('alert', ['type' => 'success', 'title' => 'Berhasil!', 'message' => $msg]);
        $this->closeModal();
    }

    // --- HAPUS DATA ---
    public function confirmDelete($id, $type)
    {
        $this->deleteId = $id;
        $this->deleteTarget = $type;
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        $idToDelete = $id;
        if (is_array($id)) {
            $idToDelete = $id['id'] ?? array_values($id)[0] ?? null;
        }

        if (empty($idToDelete)) return;

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