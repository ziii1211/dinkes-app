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

        // Mengambil PK terakhir yang status_verifikasi = 'disetujui' (Terpublikasi)
        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                    ->where('status_verifikasi', 'disetujui') 
                    ->latest('tahun')
                    ->first();

        $this->filterTahun = $lastPk ? $lastPk->tahun : date('Y');
        
        // SET DEFAULT BULAN KE BULAN SEKARANG
        $this->selectedMonth = (int) date('n');

        $this->loadPkList();
    }

    public function loadPkList()
    {
        // Filter query agar hanya mengambil data yang 'disetujui' (Terpublikasi)
        $this->pkList = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui') 
            ->get();
            
        if ($this->pkList->count() > 0) {
            // Jika ada data, otomatis pilih yang pertama
            $this->selectedPkId = $this->pkList->first()->id;
            $this->loadPkDetail();
        } else {
            // Jika kosong, reset pilihan
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
                        // Jika ada kolom target spesifik tahun (target_2025 dst), gunakan itu.
                        // Jika tidak, gunakan default 'target'.
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
            // --- REFRESH HALAMAN OTOMATIS ---
            return redirect(request()->header('Referer'));
        }
    }

    // --- FUNGSI PERBARUI BULANAN RHK ---
    public function updateBulananRhk()
    {
        // Logika untuk generate/update data bulanan (jika diperlukan logic khusus)
        // Saat ini hanya notifikasi sukses.
        
        session()->flash('message', 'Data Rencana Hasil Kerja (RHK) Bulan ini berhasil diperbarui!');
        
        // --- REFRESH HALAMAN OTOMATIS ---
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.pengaturan-kinerja');
    }
}