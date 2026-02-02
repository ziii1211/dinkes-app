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
    public $pkList = []; // Bukan lagi collection model, tapi array opsi simpel
    public $selectedPkOption = ''; // Property baru untuk dropdown simpel
    public $selectedPkId = ''; // Tetap dipakai untuk menyimpan ID PK bulan aktif
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
        $this->selectedMonth = (int) date('n');

        $this->checkPkAvailability();
    }

    /**
     * [UBAH] Cek ketersediaan PK di tahun terpilih
     * Jika ada, buat 1 opsi generic: "PK [Jabatan] Tahun [Tahun]"
     */
    public function checkPkAvailability()
    {
        $exists = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui')
            ->exists();
            
        $this->pkList = [];
        $this->selectedPkOption = '';

        if ($exists) {
            // Buat opsi tunggal
            $label = "Perjanjian Kinerja " . $this->jabatan->nama . " Tahun " . $this->filterTahun;
            $this->pkList = [
                ['value' => 'exist', 'label' => $label]
            ];
            // Otomatis pilih opsi tersebut agar user tidak perlu klik dropdown lagi jika cuma 1
            $this->selectedPkOption = 'exist';
        } else {
            // Reset jika tidak ada data
            $this->currentPk = null;
        }
    }

    public function updatedFilterTahun()
    {
        $this->checkPkAvailability();
        $this->currentPk = null; // Reset tampilan saat ganti tahun
    }

    public function selectMonth($monthIndex)
    {
        $this->selectedMonth = $monthIndex;
        // Hanya load otomatis jika data sudah ditampilkan sebelumnya (currentPk tidak null)
        if ($this->currentPk) {
            $this->loadPkAutomatic(); 
        }
    }

    /**
     * [BARU] Fungsi yang dipanggil saat tombol "Tampilkan PK" diklik
     */
    public function tampilkanPk()
    {
        if ($this->selectedPkOption == 'exist') {
            $this->loadPkAutomatic();
        }
    }

    /**
     * Mencari PK versi terakhir berdasarkan KOLOM BULAN
     */
    public function loadPkAutomatic()
    {
        $pk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('bulan', $this->selectedMonth) // Filter Bulan Aktif
            ->latest('id')
            ->first();

        if ($pk) {
            $this->selectedPkId = $pk->id;
            $this->loadPkDetail();
        } else {
            // Jika bulan ini kosong, tapi tahunnya ada (karena opsi dropdown 'exist')
            // Kita set currentPk null, tapi biarkan UI tetap merender kerangka
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

    // --- FUNGSI HAPUS & UPDATE (TETAP SAMA) ---
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
                
                // Refresh ketersediaan setelah hapus
                $this->checkPkAvailability();
                $this->loadPkAutomatic();
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