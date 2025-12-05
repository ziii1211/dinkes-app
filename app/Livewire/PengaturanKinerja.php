<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja;

class PengaturanKinerja extends Component
{
    public $jabatan;
    public $pegawai;
    
    // Filter
    public $filterTahun;
    public $selectedMonth; // Tambahan: Bulan terpilih (1-12)
    
    // Data Seleksi
    public $selectedPkId = '';
    public $pkList = [];
    public $currentPk = null;

    public function mount($jabatanId)
    {
        $this->jabatan = Jabatan::with('pegawai')->findOrFail($jabatanId);
        $this->pegawai = $this->jabatan->pegawai;

        // Cek PK terakhir
        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                    ->where('status', 'final')
                    ->latest('tahun')
                    ->first();

        $this->filterTahun = $lastPk ? $lastPk->tahun : date('Y');
        
        // SET DEFAULT BULAN KE BULAN SEKARANG
        $this->selectedMonth = (int) date('n');

        $this->loadPkList();
    }

    public function loadPkList()
    {
        $this->pkList = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status', 'final') 
            ->get();
            
        if ($this->pkList->count() > 0) {
            $this->selectedPkId = $this->pkList->first()->id;
            $this->loadPkDetail();
        } else {
            $this->selectedPkId = '';
            $this->currentPk = null;
        }
    }

    public function updatedFilterTahun()
    {
        $this->loadPkList();
    }

    // Fungsi ganti bulan
    public function selectMonth($monthIndex)
    {
        $this->selectedMonth = $monthIndex;
    }

    public function loadPkDetail()
    {
        if ($this->selectedPkId) {
            $this->currentPk = PerjanjianKinerja::with(['sasarans.indikators'])
                ->find($this->selectedPkId);

            if ($this->currentPk) {
                $tahun = $this->currentPk->tahun;
                $colTarget = 'target_' . $tahun; 

                foreach ($this->currentPk->sasarans as $sasaran) {
                    foreach ($sasaran->indikators as $indikator) {
                        $indikator->target = $indikator->$colTarget ?? $indikator->target;
                    }
                }
            }

        } else {
            $this->currentPk = null;
        }
    }

    // --- FUNGSI HAPUS RKH ---
    public function deleteRkh()
    {
        if ($this->selectedPkId) {
            $pk = PerjanjianKinerja::find($this->selectedPkId);
            if ($pk) {
                foreach ($pk->sasarans as $sasaran) {
                    $sasaran->indikators()->delete();
                    $sasaran->delete(); 
                }
                $pk->delete(); 
            }
            $this->loadPkList();
        }
    }

    // --- FUNGSI PERBARUI BULANAN RHK ---
    public function updateBulananRhk()
    {
        // Logika untuk generate/update data bulanan
        // Di sini Anda bisa menambahkan logika simpan ke tabel target_bulanan atau sejenisnya
        
        // Simulasi notifikasi sukses (bisa diganti sweetalert nanti)
        session()->flash('message', 'Data Rencana Hasil Kerja (RHK) Bulan ini berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.pengaturan-kinerja');
    }
}