<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja;
use App\Models\RencanaAksi; 
use App\Models\RealisasiKinerja;
use App\Models\RealisasiRencanaAksi;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk cek user login

class PengukuranKinerja extends Component
{
    public $jabatan;
    public $pegawai;
    
    public $tahun;
    public $selectedMonth;
    
    public $pk = null;
    public $rencanaAksis = []; 

    // --- MODAL REALISASI IKU ---
    public $isOpenRealisasi = false;
    public $indikatorId;
    public $indikatorNama;
    public $indikatorTarget;
    public $indikatorSatuan;
    public $realisasiInput;
    public $catatanInput;

    // --- MODAL REALISASI RENCANA AKSI ---
    public $isOpenRealisasiAksi = false;
    public $aksiId;
    public $aksiNama;
    public $aksiTarget;
    public $aksiSatuan;
    public $realisasiAksiInput;

    // --- MODAL TAMBAH RENCANA AKSI (BARU) ---
    public $isOpenTambahAksi = false;
    public $formAksiNama;
    public $formAksiTarget;
    public $formAksiSatuan;

    // --- MODAL TANGGAPAN PIMPINAN (FITUR BARU) ---
    public $isOpenTanggapan = false;
    public $tanggapanInput;
    
    public function mount($jabatanId)
    {
        $this->jabatan = Jabatan::with('pegawai')->findOrFail($jabatanId);
        $this->pegawai = $this->jabatan->pegawai;

        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                    ->where('status', 'final')
                    ->latest('tahun')
                    ->first();

        $this->tahun = $lastPk ? $lastPk->tahun : date('Y');
        $this->selectedMonth = (int) date('n');

        $this->loadData();
    }

    public function selectMonth($month)
    {
        $this->selectedMonth = $month;
        $this->loadData(); 
    }

    public function loadData()
    {
        // 1. LOAD PK (RHK & IKU)
        $this->pk = PerjanjianKinerja::with(['sasarans.indikators'])
            ->where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->tahun)
            ->where('status', 'final')
            ->first();
            
        if ($this->pk) {
            $colTarget = 'target_' . $this->tahun;
            $realisasiMap = RealisasiKinerja::where('bulan', $this->selectedMonth)
                ->where('tahun', $this->tahun)
                ->get()
                ->keyBy('indikator_id');

            foreach ($this->pk->sasarans as $sasaran) {
                foreach ($sasaran->indikators as $indikator) {
                    $indikator->target_tahunan = $indikator->$colTarget ?? $indikator->target;
                    
                    $data = $realisasiMap->get($indikator->id);
                    $indikator->realisasi_bulan = $data ? $data->realisasi : null;
                    $indikator->catatan_bulan = $data ? $data->catatan : null;
                    
                    // --- UPDATE BARU: Load Data Tanggapan ---
                    $indikator->tanggapan_bulan = $data ? $data->tanggapan : null; 

                    if ($indikator->realisasi_bulan !== null && $indikator->target_tahunan > 0) {
                        $capaian = ($indikator->realisasi_bulan / $indikator->target_tahunan) * 100;
                        $indikator->capaian_bulan = number_format($capaian, 2) . '%';
                    } else {
                        $indikator->capaian_bulan = '-';
                    }
                }
            }
        }

        // 2. LOAD RENCANA AKSI
        $this->rencanaAksis = RencanaAksi::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->tahun)
            ->get();

        $realisasiAksiMap = RealisasiRencanaAksi::whereIn('rencana_aksi_id', $this->rencanaAksis->pluck('id'))
            ->where('bulan', $this->selectedMonth)
            ->where('tahun', $this->tahun)
            ->get()
            ->keyBy('rencana_aksi_id');

        foreach ($this->rencanaAksis as $aksi) {
            $dataAksi = $realisasiAksiMap->get($aksi->id);
            $aksi->realisasi_bulan = $dataAksi ? $dataAksi->realisasi : null;
            
            if ($aksi->realisasi_bulan !== null && $aksi->target > 0) {
                $capaian = ($aksi->realisasi_bulan / $aksi->target) * 100;
                $aksi->capaian_bulan = round($capaian);
            } else {
                $aksi->capaian_bulan = null;
            }
        }
    }

    // --- LOGIKA TAMBAH RENCANA AKSI MANUAL ---
    public function openTambahAksi()
    {
        $this->reset(['formAksiNama', 'formAksiTarget', 'formAksiSatuan']);
        $this->isOpenTambahAksi = true;
    }

    public function closeTambahAksi()
    {
        $this->isOpenTambahAksi = false;
    }

    public function storeRencanaAksi()
    {
        $this->validate([
            'formAksiNama' => 'required',
            'formAksiTarget' => 'required|numeric',
            'formAksiSatuan' => 'required',
        ]);

        RencanaAksi::create([
            'jabatan_id' => $this->jabatan->id,
            'tahun' => $this->tahun,
            'nama_aksi' => $this->formAksiNama,
            'target' => $this->formAksiTarget,
            'satuan' => $this->formAksiSatuan,
        ]);

        $this->closeTambahAksi();
        $this->loadData();
        session()->flash('message', 'Rencana Aksi berhasil ditambahkan.');
    }

    // --- LOGIKA REALISASI IKU ---
    public function openRealisasi($indikatorId, $nama, $target, $satuan)
    {
        $this->indikatorId = $indikatorId;
        $this->indikatorNama = $nama;
        $this->indikatorTarget = $target;
        $this->indikatorSatuan = $satuan;
        
        $data = RealisasiKinerja::where('indikator_id', $indikatorId)
            ->where('bulan', $this->selectedMonth)
            ->where('tahun', $this->tahun)
            ->first();

        $this->realisasiInput = $data ? $data->realisasi : ''; 
        $this->catatanInput = $data ? $data->catatan : '';
        $this->isOpenRealisasi = true;
    }
    
    public function closeRealisasi() { $this->isOpenRealisasi = false; }

    public function simpanRealisasi()
    {
        $this->validate(['realisasiInput' => 'required|numeric']);
        RealisasiKinerja::updateOrCreate(
            ['indikator_id' => $this->indikatorId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun],
            ['realisasi' => $this->realisasiInput, 'catatan' => $this->catatanInput]
        );
        $this->closeRealisasi();
        $this->loadData(); 
        session()->flash('message', 'Realisasi IKU berhasil disimpan.');
    }

    // --- LOGIKA REALISASI RENCANA AKSI ---
    public function openRealisasiAksi($aksiId)
    {
        $this->aksiId = $aksiId;
        $rencanaAksi = RencanaAksi::find($aksiId);
        if ($rencanaAksi) {
            $this->aksiNama = $rencanaAksi->nama_aksi;
            $this->aksiTarget = $rencanaAksi->target;
            $this->aksiSatuan = $rencanaAksi->satuan;
        }
        $data = RealisasiRencanaAksi::where('rencana_aksi_id', $aksiId)
            ->where('bulan', $this->selectedMonth)
            ->where('tahun', $this->tahun)
            ->first();
        $this->realisasiAksiInput = $data ? $data->realisasi : '';
        $this->isOpenRealisasiAksi = true;
    }

    public function closeRealisasiAksi() { $this->isOpenRealisasiAksi = false; }

    public function simpanRealisasiAksi()
    {
        $this->validate(['realisasiAksiInput' => 'required|numeric']);
        RealisasiRencanaAksi::updateOrCreate(
            ['rencana_aksi_id' => $this->aksiId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun],
            ['realisasi' => $this->realisasiAksiInput]
        );
        $this->closeRealisasiAksi();
        $this->loadData(); 
        session()->flash('message', 'Realisasi Rencana Aksi berhasil disimpan.');
    }

    // =========================================================
    //  METHOD BARU: TANGGAPAN PIMPINAN
    // =========================================================

    public function openTanggapan($indikatorId, $nama)
    {
        $this->indikatorId = $indikatorId;
        $this->indikatorNama = $nama;
        
        // Ambil data existing
        $data = RealisasiKinerja::where('indikator_id', $indikatorId)
            ->where('bulan', $this->selectedMonth)
            ->where('tahun', $this->tahun)
            ->first();

        // Isi input dengan data yang sudah ada (jika ada)
        $this->tanggapanInput = $data ? $data->tanggapan : '';
        $this->isOpenTanggapan = true;
    }

    public function closeTanggapan()
    {
        $this->isOpenTanggapan = false;
        $this->reset('tanggapanInput');
    }

    public function simpanTanggapan()
    {
        // Pengecekan keamanan: Hanya pimpinan yang boleh simpan
        // Pastikan User model Anda memiliki kolom/method 'role'
        // Jika belum ada sistem role, baris ini bisa di-comment dulu
        if (Auth::check() && Auth::user()->role !== 'pimpinan') {
            session()->flash('message', 'Hanya Pimpinan yang dapat memberi tanggapan.');
            return;
        }

        RealisasiKinerja::updateOrCreate(
            [
                'indikator_id' => $this->indikatorId, 
                'bulan' => $this->selectedMonth, 
                'tahun' => $this->tahun
            ],
            [
                'tanggapan' => $this->tanggapanInput
            ]
        );

        $this->closeTanggapan();
        $this->loadData();
        session()->flash('message', 'Tanggapan berhasil disimpan.');
    }

    public function render()
    {
        $totalRhk = $this->pk ? $this->pk->sasarans->count() : 0;
        $totalIndikator = 0;
        $filledIndikator = 0;
        if ($this->pk) {
            foreach ($this->pk->sasarans as $s) {
                foreach ($s->indikators as $i) {
                    $totalIndikator++;
                    if ($i->realisasi_bulan !== null) $filledIndikator++;
                }
            }
        }
        $persenTerisi = $totalIndikator > 0 ? round(($filledIndikator / $totalIndikator) * 100) : 0;
        
        return view('livewire.pengukuran-kinerja', [
            'totalRhk' => $totalRhk,
            'totalIndikator' => $totalIndikator,
            'filledIndikator' => $filledIndikator,
            'persenTerisi' => $persenTerisi,
            'months' => [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ]
        ]);
    }
}