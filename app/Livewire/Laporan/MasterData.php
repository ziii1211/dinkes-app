<?php

namespace App\Livewire\Laporan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\IndikatorSubKegiatan;
use Livewire\Attributes\Title;
use App\Models\Jabatan;

class MasterData extends Component
{
    use WithPagination;

    // --- PROPERTI UI ---
    public $isOpen = false;
    public $isOpenIndikator = false;
    public $isOpenPenanggungJawab = false;
    public $isEditMode = false;

    // Properti untuk Show Entries (Default ganti ke 10 biar lebih rapi)
    public $perPage = 10;

    // --- PROPERTI FILTER TAHUN (BARU) ---
    public $tahun; 
    public $tahunOptions = [];

    // --- PROPERTI HAPUS ---
    public $deleteTarget = '';
    public $deleteId = null;

    // --- PROPERTI FORM UTAMA (Program/Kegiatan/Sub) ---
    public $formType = 'program'; // Opsi: 'program', 'kegiatan', 'sub_kegiatan'
    public $parentId = null;
    public $dataId = null;
    public $kode, $nama;

    // --- PROPERTI TAMBAHAN ---
    public $pagu;
    public $target; 

    // --- PROPERTI MODAL INDIKATOR ---
    public $selectedSubKegiatan = null;
    public $indikators = [];
    public $indikatorId = null;
    public $subOutput, $satuan;

    // --- PROPERTI PENANGGUNG JAWAB ---
    public $jabatans = [];
    public $selectedJabatanId = null;

    // Listener Event
    protected $listeners = ['deleteConfirmed' => 'delete'];

    // --- INITIALIZE (BARU) ---
    public function mount()
    {
        // Set Default Tahun ke 2026 (sesuai data yang ada)
        $this->tahun = 2026; 
        
        // Opsi Tahun untuk Dropdown
        $this->tahunOptions = [2025, 2026, 2027, 2028, 2029, 2030];
    }

    #[Title('Master Data Laporan')]
    public function render()
    {
        // Query Data: Program -> Kegiatan -> Sub Kegiatan
        // DITAMBAHKAN: Filter where('tahun', $this->tahun)
        $programs = Program::where('tahun', $this->tahun) 
            ->with(['kegiatans' => function ($q) {
                $q->orderBy('kode', 'asc')
                    ->orderBy('id', 'asc')
                    ->with(['subKegiatans' => function ($sub) {
                        $sub->orderBy('kode', 'asc')
                            ->orderBy('id', 'asc')
                            ->with('jabatan');
                    }]);
            }])
            // Logika: Kode berawalan 'X' atau 'x' diberi prioritas 0 (Paling Atas), sisanya 1
            ->orderByRaw("CASE WHEN kode LIKE 'X%' OR kode LIKE 'x%' THEN 0 ELSE 1 END ASC")
            ->orderBy('kode', 'asc') 
            ->orderBy('id', 'asc')
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
        $this->pagu = null;
        $this->target = null; 
        $this->dataId = null;
        $this->parentId = null;
        $this->isEditMode = false;

        // Reset Form Indikator
        $this->indikatorId = null;
        $this->subOutput = null;
        $this->satuan = null;

        // Reset Penanggung Jawab
        $this->selectedJabatanId = null;
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

    public function closePenanggungJawabModal()
    {
        $this->isOpenPenanggungJawab = false;
        $this->selectedSubKegiatan = null;
        $this->resetInput();
    }

    // --- LOGIKA FORM UTAMA (CREATE & EDIT) ---

    // 1. Program
    public function createProgram()
    {
        $this->setupForm('program');
    }
    public function editProgram($id)
    {
        $this->loadForm('program', $id);
    }

    // 2. Kegiatan
    public function createKegiatan($parentId)
    {
        $this->setupForm('kegiatan', $parentId);
    }
    public function editKegiatan($id)
    {
        $this->loadForm('kegiatan', $id);
    }

    // 3. Sub Kegiatan
    public function createSubKegiatan($parentId)
    {
        $this->setupForm('sub_kegiatan', $parentId);
    }
    public function editSubKegiatan($id)
    {
        $this->loadForm('sub_kegiatan', $id);
    }

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
        $model = match ($type) {
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

        // Load Pagu (Format Rupiah untuk tampilan)
        $this->pagu = $data->pagu ? number_format($data->pagu, 0, ',', '.') : '';
        
        // Set Parent ID sesuai tipe
        if ($type == 'kegiatan') $this->parentId = $data->program_id;
        if ($type == 'sub_kegiatan') $this->parentId = $data->kegiatan_id;

        $this->isEditMode = true;
        $this->isOpen = true;
    }

    // Helper Bersihkan Rupiah
    private function cleanRupiah($val)
    {
        if (is_null($val) || $val === '') return 0;
        $clean = preg_replace('/[^0-9]/', '', $val);
        return (float) $clean;
    }

    // Simpan Data Utama
    public function store()
    {
        $this->validate([
            'kode' => 'required',
            'nama' => 'required'
        ]);

        $data = [
            'kode' => $this->kode,
            'nama' => $this->nama,
            'tahun' => $this->tahun, // <--- DITAMBAHKAN: Simpan Tahun
        ];

        // Set Pagu hanya jika form yang disubmit adalah sub_kegiatan
        if ($this->formType == 'sub_kegiatan') {
            $data['pagu'] = $this->cleanRupiah($this->pagu);
        } else {
            $data['pagu'] = 0;
        }

        if ($this->formType == 'program') {
            Program::updateOrCreate(['id' => $this->dataId], $data);
        } elseif ($this->formType == 'kegiatan') {
            $data['program_id'] = $this->parentId;
            Kegiatan::updateOrCreate(['id' => $this->dataId], $data);
        } elseif ($this->formType == 'sub_kegiatan') {
            $data['kegiatan_id'] = $this->parentId;
            SubKegiatan::updateOrCreate(['id' => $this->dataId], $data);
        }

        $this->dispatch('alert', ['type' => 'success', 'title' => 'Berhasil!', 'message' => 'Data berhasil disimpan untuk tahun ' . $this->tahun]);
        $this->closeModal();
    }

    // --- LOGIKA PENANGGUNG JAWAB ---

    public function openPenanggungJawab($subKegiatanId)
    {
        $this->resetInput();
        $this->selectedSubKegiatan = SubKegiatan::findOrFail($subKegiatanId);
        $this->selectedJabatanId = $this->selectedSubKegiatan->jabatan_id;
        $this->jabatans = Jabatan::orderBy('nama', 'asc')->get();
        $this->isOpenPenanggungJawab = true;
    }

    public function savePenanggungJawab()
    {
        if ($this->selectedSubKegiatan) {
            $this->selectedSubKegiatan->update([
                'jabatan_id' => $this->selectedJabatanId
            ]);

            $this->dispatch('alert', [
                'type' => 'success',
                'title' => 'Berhasil!',
                'message' => 'Penanggung Jawab berhasil diperbarui.'
            ]);
        }

        $this->closePenanggungJawabModal();
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
        $this->target = $indikator->target; 
    }

    public function saveIndikator()
    {
        $this->validate([
            'subOutput' => 'required',
            'satuan' => 'required',
            'target' => 'required',
        ]);

        IndikatorSubKegiatan::updateOrCreate(
            ['id' => $this->indikatorId],
            [
                'sub_kegiatan_id' => $this->selectedSubKegiatan->id,
                'keterangan' => $this->subOutput,
                'satuan' => $this->satuan,
                'target' => $this->target,
                'tahun' => $this->tahun // <--- DITAMBAHKAN: Simpan Tahun Indikator
            ]
        );

        $this->openIndikator($this->selectedSubKegiatan->id); // Refresh list

        $this->indikatorId = null;
        $this->subOutput = null;
        $this->satuan = null;
        $this->target = null;

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