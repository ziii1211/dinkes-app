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
    
    public $judul;
    public $periode; // Format YYYY-MM untuk Flatpickr

    // Helper: Mapping Bulan
    protected $monthsMap = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

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
        $this->periode = date('Y-m'); // Default bulan ini
        $this->openModal();
    }

    public function edit($id)
    {
        $laporan = LaporanKonsolidasi::findOrFail($id);
        $this->laporanId = $id;
        $this->judul = $laporan->judul;
        
        // Konversi "Januari 2025" dari DB ke "2025-01" untuk Flatpickr
        $bulanNum = array_search($laporan->bulan, $this->monthsMap) ?: '01'; 
        $this->periode = $laporan->tahun . '-' . $bulanNum;
        
        $this->isEdit = true;
        $this->openModal();
    }

    public function save()
    {
        $this->validate([
            'judul' => 'required',
            'periode' => 'required', // Wajib format YYYY-MM
        ]);

        // Pecah "2025-05" jadi Tahun & Nama Bulan
        $parts = explode('-', $this->periode);
        if(count($parts) == 2){
            $tahun = $parts[0];
            $bulanNum = $parts[1];
            $namaBulan = $this->monthsMap[$bulanNum] ?? 'Januari';

            LaporanKonsolidasi::updateOrCreate(
                ['id' => $this->laporanId],
                [
                    'judul' => $this->judul,
                    'bulan' => $namaBulan,
                    'tahun' => $tahun,
                ]
            );

            session()->flash('message', $this->laporanId ? 'Laporan berhasil diperbarui.' : 'Laporan berhasil dibuat.');
            $this->closeModal();
        }
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
        $this->judul = '';
        $this->periode = '';
        $this->laporanId = null;
        $this->isEdit = false;
        $this->resetErrorBag();
    }
}