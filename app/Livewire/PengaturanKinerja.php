<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja;
use Carbon\Carbon; // Pastikan Import Carbon ada

class PengaturanKinerja extends Component
{
    public $jabatan;
    public $pegawai;
    
    // Filter
    public $filterTahun;
    public $selectedMonth;
    
    // Data Seleksi
    public $selectedPkId = '';
    public $pkList = [];
    public $currentPk = null;

    public function mount($jabatanId)
    {
        $this->jabatan = Jabatan::with('pegawai')->findOrFail($jabatanId);
        $this->pegawai = $this->jabatan->pegawai;

        // Mengambil PK terakhir untuk set default tahun
        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                    ->where('status_verifikasi', 'disetujui') 
                    ->latest('tahun')
                    ->first();

        $this->filterTahun = $lastPk ? $lastPk->tahun : date('Y');
        
        // Default ke bulan sekarang
        $this->selectedMonth = (int) date('n');

        // 1. Load List untuk Dropdown (Opsional)
        $this->loadPkList();

        // 2. Load PK Secara Cerdas berdasarkan Bulan Sekarang
        $this->loadPkAutomatic();
    }

    /**
     * Mengambil daftar semua PK di tahun tersebut (untuk Dropdown)
     */
    public function loadPkList()
    {
        $this->pkList = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui') 
            ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
            ->get();
            
        // Catatan: Kita hapus logika "auto select first" di sini 
        // karena akan ditangani oleh loadPkAutomatic()
        if ($this->pkList->isEmpty()) {
            $this->selectedPkId = '';
            $this->currentPk = null;
        }
    }

    public function updatedFilterTahun()
    {
        $this->loadPkList();
        $this->loadPkAutomatic(); // Reset pilihan ke data yang relevan tahun baru
    }

    /**
     * LOGIKA UTAMA: Ganti Bulan -> Cari Data PK yang Sesuai
     */
    public function selectMonth($monthIndex)
    {
        $this->selectedMonth = $monthIndex;
        $this->loadPkAutomatic(); // <--- INI KUNCINYA
    }

    /**
     * Mencari PK versi terakhir yang dibuat PADA atau SEBELUM bulan yang dipilih.
     */
    public function loadPkAutomatic()
    {
        // Cari PK yang:
        // 1. Tahun sesuai filter
        // 2. Status Disetujui
        // 3. Tanggal buat (created_at) <= Bulan yang dipilih
        // 4. Ambil yang paling baru (Latest)
        
        $pk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->filterTahun)
            ->where('status_verifikasi', 'disetujui')
            ->where(function($query) {
                $query->whereYear('created_at', $this->filterTahun)
                      ->whereMonth('created_at', '<=', $this->selectedMonth);
            })
            ->latest('created_at')
            ->first();

        if ($pk) {
            $this->selectedPkId = $pk->id;
            $this->loadPkDetail(); // Tampilkan datanya
        } else {
            // Jika tidak ada data di bulan tersebut (misal pilih Jan tapi data baru dibuat Maret)
            // Maka kosongkan atau bisa cari fallback lain jika mau.
            $this->selectedPkId = '';
            $this->currentPk = null;
        }
    }

    /**
     * Load Detail PK berdasarkan ID yang terpilih
     */
    public function loadPkDetail()
    {
        if ($this->selectedPkId) {
            $this->currentPk = PerjanjianKinerja::with(['sasarans.indikators'])
                ->find($this->selectedPkId);

            if ($this->currentPk) {
                // Gunakan target spesifik tahun jika ada logic revisi target
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
            return redirect(request()->header('Referer'));
        }
    }

    // --- FUNGSI PERBARUI BULANAN RHK ---
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