<?php

namespace App\Livewire\LaporanKonsolidasi;

use Livewire\Component;
use App\Models\LaporanKonsolidasi;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // --- FITUR UTAMA ---
    public $search = '';
    public $perPage = 10;
    public $filterTahun = ''; // Filter di Tabel
    
    // --- MODAL FORM ---
    public $isOpen = false;
    public $isEdit = false;
    public $laporanId;
    
    // Form Input
    public $bulan;
    public $tahun;

    // Reset pagination saat filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTahun() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function render()
    {
        $query = LaporanKonsolidasi::query();

        // Pencarian
        if (!empty($this->search)) {
            $query->where('judul', 'like', '%' . $this->search . '%');
        }

        // Filter Tahun
        if (!empty($this->filterTahun)) {
            $query->where('tahun', $this->filterTahun);
        }

        // Data Tahun untuk Dropdown Filter
        $availableYears = LaporanKonsolidasi::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('livewire.laporan-konsolidasi.index', [
            'laporans' => $query->latest()->paginate($this->perPage),
            'availableYears' => $availableYears
        ]);
    }

    // --- LOGIC MODAL & CRUD ---

    public function create()
    {
        $this->resetInputFields();
        $this->tahun = date('Y'); // Default tahun ini
        $this->openModal();
    }

    public function edit($id)
    {
        $laporan = LaporanKonsolidasi::findOrFail($id);
        $this->laporanId = $id;
        $this->bulan = $laporan->bulan;
        $this->tahun = $laporan->tahun;
        
        $this->isEdit = true;
        $this->openModal();
    }

    public function store() // Fungsi ini dipanggil dari View 'save'
    {
        $this->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        // GENERATE JUDUL OTOMATIS (UPDATE SESUAI REQUEST)
        // Format: "Laporan Konsolidasi Bulan [Bulan] Tahun Anggaran [Tahun]"
        $judulOtomatis = "Laporan Konsolidasi Bulan " . $this->bulan . " Tahun Anggaran " . $this->tahun;

        LaporanKonsolidasi::updateOrCreate(
            ['id' => $this->laporanId],
            [
                'judul' => $judulOtomatis,
                'bulan' => $this->bulan,
                'tahun' => $this->tahun,
            ]
        );

        session()->flash('message', $this->laporanId ? 'Laporan berhasil diperbarui.' : 'Laporan berhasil dibuat.');
        $this->closeModal();
    }

    // Alias agar sesuai dengan wire:click="save" di view
    public function save() {
        $this->store();
    }

    public function delete($id)
    {
        LaporanKonsolidasi::find($id)->delete();
        session()->flash('message', 'Laporan berhasil dihapus.');
    }

    public function openModal() { $this->isOpen = true; }
    
    public function closeModal() 
    { 
        $this->isOpen = false; 
        $this->resetInputFields(); 
    }

    public function resetInputFields()
    {
        $this->laporanId = null;
        $this->bulan = null;
        $this->tahun = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }
}