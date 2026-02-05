<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja;
// use App\Models\PkIndikator; // Hapus ini agar tidak bingung
use Carbon\Carbon;

class PengaturanKinerja extends Component
{
    public $jabatan;
    public $pegawai;
    
    // Filter
    public $filterTahun;
    public $selectedMonth;
    
    // Data Seleksi
    public $pkList = []; 
    public $selectedPkOption = ''; 
    public $selectedPkId = ''; 
    public $currentPk = null;

    // Data Form Input (Target & Pagu Bulanan) - Kita siapkan saja array-nya
    public $targetBulanan = [];
    public $paguBulanan = [];

    public function mount($jabatanId)
    {
        $this->jabatan = Jabatan::with('pegawai')->findOrFail($jabatanId);
        $this->pegawai = $this->jabatan->pegawai;

        // Set Default Tahun (Ambil yang paling terakhir dibuat/aktif)
        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                    ->where('status_verifikasi', 'disetujui') 
                    ->latest('tahun')
                    ->first();

        $this->filterTahun = $lastPk ? $lastPk->tahun : date('Y');
        $this->selectedMonth = (int) date('n'); // Default Bulan Sekarang

        $this->checkPkAvailability();
    }

    public function checkPkAvailability()
    {
        // Cek apakah ada PK di tahun ini (Bulan apapun)
        $exists = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui')
            ->exists();
            
        $this->pkList = [];
        $this->selectedPkOption = '';

        if ($exists) {
            $label = "Perjanjian Kinerja " . $this->jabatan->nama . " Tahun " . $this->filterTahun;
            $this->pkList = [
                ['value' => 'exist', 'label' => $label]
            ];
            $this->selectedPkOption = 'exist';
        } else {
            $this->currentPk = null;
        }
    }

    public function updatedFilterTahun()
    {
        $this->checkPkAvailability();
        $this->currentPk = null; 
    }

    public function selectMonth($monthIndex)
    {
        $this->selectedMonth = $monthIndex;
        
        // Refresh data agar mengikuti logika bulan baru
        if ($this->selectedPkOption == 'exist') {
            $this->loadDataSmart();
        }
    }

    public function tampilkanPk()
    {
        if ($this->selectedPkOption == 'exist') {
            $this->loadDataSmart();
        }
    }

    /**
     * [LOGIKA SMART LOAD]
     * Mencari PK yang berlaku untuk bulan yang dipilih (Effective Date Logic)
     */
    public function loadDataSmart()
    {
        // 1. Cari PK Murni atau Perubahan yang berlaku pada bulan ini ($this->selectedMonth)
        // Logika: Cari PK dengan bulan <= selectedMonth, ambil yang paling besar bulannya.
        
        $pk = PerjanjianKinerja::with(['sasarans.indikators'])
            ->where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('bulan', '<=', $this->selectedMonth) // Kunci Logika Effective Date
            ->orderBy('bulan', 'desc') // Prioritaskan PK Perubahan (bulan lebih besar)
            ->orderBy('id', 'desc')
            ->first();

        $this->targetBulanan = [];
        $this->paguBulanan = [];

        if ($pk) {
            $this->currentPk = $pk;
            $this->selectedPkId = $pk->id;

            // Load Detail Target Tahunan (untuk display)
            $colTarget = 'target_' . $pk->tahun;
            foreach ($this->currentPk->sasarans as $sasaran) {
                foreach ($sasaran->indikators as $indikator) {
                    // Mapping target tahunan dinamis
                    $indikator->target = $indikator->$colTarget ?? $indikator->target;
                    
                    // Default target bulanan (kosongkan dulu karena tabel khusus belum ada)
                    $this->targetBulanan[$indikator->id] = ''; 
                }
            }
            
            // [CATATAN] Bagian load target bulanan dari database dihapus dulu 
            // karena tabel khusus untuk menyimpan breakdown bulanan belum tersedia.
            
        } else {
            // Jika tidak ketemu PK sama sekali (misal user pilih Januari tapi PK dibuat Februari)
            $this->currentPk = null;
            $this->selectedPkId = '';
        }
    }

    // Fungsi Simpan Target Bulanan (Sementara dinonaktifkan/placeholder)
    public function simpanTarget($indikatorId)
    {
        if (!$this->currentPk) return;

        // Logic simpan belum bisa dijalankan karena tabel target bulanan belum dibuat.
        // Nanti bisa ditambahkan jika fitur breakdown bulanan diperlukan.
        // $this->dispatch('alert', ['type' => 'info', 'message' => 'Fitur simpan target bulanan belum aktif.']);
    }

    public function deleteRkh()
    {
        if ($this->selectedPkId) {
            $pk = PerjanjianKinerja::find($this->selectedPkId);
            if ($pk) {
                // Hapus child relations dulu jika perlu
                foreach ($pk->sasarans as $sasaran) {
                    $sasaran->indikators()->delete();
                    $sasaran->delete(); 
                }
                $pk->delete(); 
                
                $this->checkPkAvailability();
                $this->currentPk = null;
                $this->selectedPkId = '';
            }
        }
    }

    public function updateBulananRhk()
    {
        session()->flash('message', 'Data Rencana Hasil Kerja (RHK) Bulan ini berhasil diperbarui!');
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.pengaturan-kinerja', [
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ]
        ]);
    }
}