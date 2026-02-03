<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja;
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

    public function mount($jabatanId)
    {
        $this->jabatan = Jabatan::with('pegawai')->findOrFail($jabatanId);
        $this->pegawai = $this->jabatan->pegawai;

        // Set Default Tahun
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
        if ($this->currentPk) {
            $this->loadPkAutomatic(false); // False: Jangan auto-switch bulan jika manual klik tab
        }
    }

    public function tampilkanPk()
    {
        if ($this->selectedPkOption == 'exist') {
            $this->loadPkAutomatic(true); // True: Boleh auto-switch bulan jika kosong
        }
    }

    /**
     * [PERBAIKAN] Logika Load PK Lebih Pintar
     * @param bool $autoSwitchMonth Jika true, sistem akan mencari bulan lain jika bulan terpilih kosong
     */
    public function loadPkAutomatic($autoSwitchMonth = true)
    {
        // 1. Coba cari di bulan yang sedang dipilih (selectedMonth)
        $pk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('bulan', $this->selectedMonth) 
            ->latest('id')
            ->first();

        // 2. [LOGIKA BARU] Jika kosong & mode auto-switch aktif (saat tombol Tampilkan diklik)
        // Cari data di bulan apa saja yang tersedia (ambil yang paling baru)
        if (!$pk && $autoSwitchMonth) {
            $lastAvailablePk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                ->where('tahun', $this->filterTahun)
                ->where('status_verifikasi', 'disetujui')
                ->orderBy('bulan', 'desc') // Prioritaskan bulan paling akhir
                ->first();

            if ($lastAvailablePk) {
                // KETEMU! Pindahkan tampilan ke bulan tersebut
                $this->selectedMonth = $lastAvailablePk->bulan;
                $pk = $lastAvailablePk;
            }
        }

        // 3. Tampilkan Data
        if ($pk) {
            $this->selectedPkId = $pk->id;
            $this->loadPkDetail();
        } else {
            $this->selectedPkId = '';
            $this->currentPk = null;
        }
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
        }
    }

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
                
                $this->checkPkAvailability();
                
                // Reload lagi, siapa tahu masih ada data di bulan lain
                $this->loadPkAutomatic(true);
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
        return view('livewire.pengaturan-kinerja');
    }
}