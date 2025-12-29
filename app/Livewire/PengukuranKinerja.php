<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja;
use App\Models\RencanaAksi; 
use App\Models\RealisasiKinerja;
use App\Models\RealisasiRencanaAksi;
use App\Models\JadwalPengukuran;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengukuranKinerja extends Component
{
    public $jabatan;
    public $pegawai;
    
    public $tahun;
    public $selectedMonth;
    
    public $availableYears = []; 

    public $pk = null;
    public $rencanaAksis = []; 

    // Status Jadwal
    public $isScheduleOpen = false;
    public $deadlineDate = null;
    public $scheduleMessage = '';

    // --- MODAL STATES ---
    public $isOpenRealisasi = false;
    public $isOpenRealisasiAksi = false;
    public $isOpenTambahAksi = false;
    public $isOpenTanggapan = false;
    public $isOpenAturJadwal = false;

    // Form Inputs
    public $formJadwalMulai, $formJadwalSelesai;
    
    // Updated: Added capaianInput and showCapaianInput
    public $indikatorId, $indikatorNama, $indikatorTarget, $indikatorSatuan;
    public $realisasiInput, $capaianInput, $catatanInput; 
    public $showCapaianInput = false;

    public $aksiId, $aksiNama, $aksiTarget, $aksiSatuan, $realisasiAksiInput;
    public $formAksiNama, $formAksiTarget, $formAksiSatuan;
    public $tanggapanInput;
    
    public function mount($jabatanId)
    {
        $this->jabatan = Jabatan::with('pegawai')->findOrFail($jabatanId);
        $this->pegawai = $this->jabatan->pegawai;

        // Ambil PK terakhir yang disetujui
        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                    ->where('status_verifikasi', 'disetujui')
                    ->latest('tahun')
                    ->first();

        $this->tahun = $lastPk ? $lastPk->tahun : date('Y');
        $this->selectedMonth = (int) date('n');

        // MEMBUAT LIST TAHUN (Wajib ada untuk dropdown)
        $currentYear = date('Y');
        $this->availableYears = range($currentYear - 2, $currentYear + 5);

        $this->loadData();
    }

    // FUNGSI UNTUK MENGUBAH TAHUN
    public function setTahun($year)
    {
        $this->tahun = $year;
        $this->loadData(); // Reload data sesuai tahun baru
    }

    public function selectMonth($month)
    {
        $this->selectedMonth = $month;
        $this->loadData(); 
    }

    // HELPER: Konversi angka string (koma) ke float (titik)
    private function parseNumber($value)
    {
        if (is_null($value)) return 0;
        // Ganti koma dengan titik, lalu cast ke float
        return (float) str_replace(',', '.', (string) $value);
    }

    public function loadData()
    {
        $this->checkScheduleStatus();

        // Ambil PK sesuai tahun yang dipilih ($this->tahun)
        $this->pk = PerjanjianKinerja::with(['sasarans.indikators'])
            ->where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->tahun)
            ->where('status_verifikasi', 'disetujui')
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
                    $indikator->tanggapan_bulan = $data ? $data->tanggapan : null; 

                    // --- LOGIKA PERHITUNGAN CAPAIAN ---
                    
                    // 1. Cek apakah ada Capaian Manual di Database?
                    if ($data && $data->capaian !== null) {
                        // Jika ada manual (biasanya untuk indikator arah turun), gunakan itu
                        $indikator->capaian_bulan = number_format($data->capaian, 2, ',', '.') . '%';
                    } 
                    // 2. Jika tidak ada manual, hitung otomatis jika realisasi terisi
                    elseif ($indikator->realisasi_bulan !== null) {
                        $target = $this->parseNumber($indikator->target_tahunan);
                        $realisasi = $this->parseNumber($indikator->realisasi_bulan);
                        
                        if ($target > 0) {
                            // Deteksi Arah
                            $arah = strtolower(trim($indikator->arah ?? ''));
                            $isNegative = in_array($arah, ['menurun', 'turun', 'negative', 'negatif', 'min']);

                            if ($isNegative) {
                                // Rumus Negatif (Turun) Default
                                // ((2 * Target) - Realisasi) / Target * 100
                                $capaian = ((2 * $target) - $realisasi) / $target * 100;
                            } else {
                                // Rumus Positif (Naik) Default
                                // (Realisasi / Target) * 100
                                $capaian = ($realisasi / $target) * 100;
                            }
                            
                            // Capping 100% dan floor 0%
                            if ($capaian > 100) $capaian = 100;
                            if ($capaian < 0) $capaian = 0;

                            $indikator->capaian_bulan = number_format($capaian, 2, ',', '.') . '%';
                        } else {
                            $indikator->capaian_bulan = '-';
                        }
                    } else {
                        $indikator->capaian_bulan = '-';
                    }
                }
            }
        }

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
            
            // Gunakan parseNumber
            $targetAksi = $this->parseNumber($aksi->target);
            $realisasiAksi = $this->parseNumber($aksi->realisasi_bulan);

            if ($aksi->realisasi_bulan !== null && $targetAksi > 0) {
                // Rencana Aksi Default Positif
                $capaian = ($realisasiAksi / $targetAksi) * 100;
                
                if ($capaian > 100) $capaian = 100;
                if ($capaian < 0) $capaian = 0;
                
                $aksi->capaian_bulan = round($capaian);
            } else {
                $aksi->capaian_bulan = null;
            }
        }
    }

    public function checkScheduleStatus()
    {
        if (!class_exists(JadwalPengukuran::class)) {
            $this->isScheduleOpen = true; 
            return;
        }

        $jadwal = JadwalPengukuran::where('tahun', $this->tahun)
                    ->where('bulan', $this->selectedMonth)
                    ->first();

        $now = Carbon::now();

        if ($jadwal && $jadwal->is_active) {
            $start = Carbon::parse($jadwal->tanggal_mulai)->startOfDay();
            $end = Carbon::parse($jadwal->tanggal_selesai)->endOfDay();
            
            $this->deadlineDate = $end->translatedFormat('d F Y H:i');

            if ($now->between($start, $end)) {
                $this->isScheduleOpen = true;
                $diff = $now->diff($end);
                
                if ($diff->days > 0) {
                    $this->scheduleMessage = "Sisa Waktu: {$diff->days} Hari {$diff->h} Jam lagi.";
                } else {
                    $this->scheduleMessage = "Segera Berakhir: {$diff->h} Jam {$diff->i} Menit lagi.";
                }

            } else if ($now->gt($end)) {
                $this->isScheduleOpen = false;
                $this->scheduleMessage = "Batas waktu telah berakhir pada {$this->deadlineDate}.";
            } else {
                $this->isScheduleOpen = false;
                $this->scheduleMessage = "Jadwal belum dimulai. Dibuka pada " . $start->translatedFormat('d F Y');
            }
        } else {
            $this->isScheduleOpen = false;
            $this->deadlineDate = '-';
            $this->scheduleMessage = "Jadwal pengisian belum diatur oleh Admin.";
        }
    }

    public function openAturJadwal()
    {
        $jadwal = JadwalPengukuran::where('tahun', $this->tahun)
                    ->where('bulan', $this->selectedMonth)
                    ->first();
        
        if ($jadwal) {
            $this->formJadwalMulai = $jadwal->tanggal_mulai->format('Y-m-d');
            $this->formJadwalSelesai = $jadwal->tanggal_selesai->format('Y-m-d');
        } else {
            $this->formJadwalMulai = date('Y-m-d');
            $this->formJadwalSelesai = Carbon::now()->addDays(7)->format('Y-m-d');
        }
        
        $this->isOpenAturJadwal = true;
    }

    public function closeAturJadwal()
    {
        $this->isOpenAturJadwal = false;
    }

    public function simpanJadwal()
    {
        if (Auth::user()->role !== 'admin') return;

        $this->validate([
            'formJadwalMulai' => 'required|date',
            'formJadwalSelesai' => 'required|date|after_or_equal:formJadwalMulai',
        ]);

        JadwalPengukuran::updateOrCreate(
            ['tahun' => $this->tahun, 'bulan' => $this->selectedMonth],
            [
                'tanggal_mulai' => $this->formJadwalMulai,
                'tanggal_selesai' => $this->formJadwalSelesai,
                'is_active' => true
            ]
        );

        $this->closeAturJadwal();
        $this->loadData();
        session()->flash('message', 'Jadwal pengisian berhasil diperbarui.');
    }

    // --- UPDATED: Menambahkan parameter $arah ---
    public function openRealisasi($id, $nama, $target, $satuan, $arah = '') {
        $this->indikatorId = $id; 
        $this->indikatorNama = $nama; 
        $this->indikatorTarget = $target; 
        $this->indikatorSatuan = $satuan;
        
        // Deteksi apakah arah turun/negatif
        $arahClean = strtolower(trim($arah));
        $this->showCapaianInput = in_array($arahClean, ['menurun', 'turun', 'negative', 'negatif', 'min']);

        $data = RealisasiKinerja::where('indikator_id', $id)
                    ->where('bulan', $this->selectedMonth)
                    ->where('tahun', $this->tahun)
                    ->first();
        
        $this->realisasiInput = $data ? $data->realisasi : '';
        // Load capaian jika ada, ubah titik ke koma untuk input
        $this->capaianInput = $data && $data->capaian !== null ? str_replace('.', ',', $data->capaian) : '';
        $this->catatanInput = $data ? $data->catatan : '';
        
        $this->isOpenRealisasi = true;
    }

    public function closeRealisasi() { $this->isOpenRealisasi = false; }
    
    public function simpanRealisasi() {
        // Validasi input angka bisa koma atau titik
        $this->validate(['realisasiInput' => ['required', 'regex:/^\d+([.,]\d+)?$/']]);
        
        // Bersihkan format (ubah koma ke titik)
        $cleanRealisasi = str_replace(',', '.', $this->realisasiInput);
        
        // Proses Capaian (Hanya simpan jika ditampilkan/arah turun, jika tidak set null agar terhitung otomatis)
        $cleanCapaian = null;
        if ($this->showCapaianInput && $this->capaianInput !== '' && $this->capaianInput !== null) {
             $cleanCapaian = str_replace(',', '.', $this->capaianInput);
        }
        
        RealisasiKinerja::updateOrCreate(
            ['indikator_id' => $this->indikatorId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun], 
            [
                'realisasi' => $cleanRealisasi, 
                'capaian' => $cleanCapaian, // Simpan capaian manual
                'catatan' => $this->catatanInput
            ]
        );
        
        $this->closeRealisasi(); 
        $this->loadData();
    }

    public function openRealisasiAksi($id) {
        $this->aksiId = $id; $aksi = RencanaAksi::find($id);
        $this->aksiNama = $aksi->nama_aksi; $this->aksiTarget = $aksi->target; $this->aksiSatuan = $aksi->satuan;
        $data = RealisasiRencanaAksi::where('rencana_aksi_id', $id)->where('bulan', $this->selectedMonth)->where('tahun', $this->tahun)->first();
        $this->realisasiAksiInput = $data ? $data->realisasi : ''; $this->isOpenRealisasiAksi = true;
    }
    public function closeRealisasiAksi() { $this->isOpenRealisasiAksi = false; }
    public function simpanRealisasiAksi() {
        $this->validate(['realisasiAksiInput' => ['required', 'regex:/^\d+([.,]\d+)?$/']]);
        
        $cleanRealisasi = str_replace(',', '.', $this->realisasiAksiInput);
        
        RealisasiRencanaAksi::updateOrCreate(['rencana_aksi_id' => $this->aksiId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun], ['realisasi' => $cleanRealisasi]);
        $this->closeRealisasiAksi(); $this->loadData();
    }
    public function openTambahAksi() { $this->reset(['formAksiNama', 'formAksiTarget', 'formAksiSatuan']); $this->isOpenTambahAksi = true; }
    public function closeTambahAksi() { $this->isOpenTambahAksi = false; }
    public function storeRencanaAksi() {
        $this->validate(['formAksiNama' => 'required', 'formAksiTarget' => 'required', 'formAksiSatuan' => 'required']);
        
        $cleanTarget = str_replace(',', '.', $this->formAksiTarget);
        
        RencanaAksi::create(['jabatan_id' => $this->jabatan->id, 'tahun' => $this->tahun, 'nama_aksi' => $this->formAksiNama, 'target' => $cleanTarget, 'satuan' => $this->formAksiSatuan]);
        $this->closeTambahAksi(); $this->loadData();
    }

    public function deleteRencanaAksi($id)
    {
        $aksi = RencanaAksi::find($id);

        if ($aksi) {
            RealisasiRencanaAksi::where('rencana_aksi_id', $id)->delete();
            $aksi->delete();
            $this->loadData();
            session()->flash('message', 'Rencana Aksi berhasil dihapus.');
        }
    }

    public function openTanggapan($id, $nama) {
        $this->indikatorId = $id; $this->indikatorNama = $nama;
        $data = RealisasiKinerja::where('indikator_id', $id)->where('bulan', $this->selectedMonth)->where('tahun', $this->tahun)->first();
        $this->tanggapanInput = $data ? $data->tanggapan : ''; $this->isOpenTanggapan = true;
    }
    public function closeTanggapan() { $this->isOpenTanggapan = false; }
    public function simpanTanggapan() {
        if (Auth::user()->role !== 'pimpinan') return;
        RealisasiKinerja::updateOrCreate(['indikator_id' => $this->indikatorId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun], ['tanggapan' => $this->tanggapanInput]);
        $this->closeTanggapan(); $this->loadData();
    }

    public function render()
    {
        $totalRhk = $this->pk ? $this->pk->sasarans->count() : 0;
        $totalIndikator = 0; $filledIndikator = 0;
        if ($this->pk) {
            foreach ($this->pk->sasarans as $s) {
                foreach ($s->indikators as $i) {
                    $totalIndikator++; if ($i->realisasi_bulan !== null) $filledIndikator++;
                }
            }
        }
        $persenTerisi = $totalIndikator > 0 ? round(($filledIndikator / $totalIndikator) * 100) : 0;
        
        return view('livewire.pengukuran-kinerja', [
            'totalRhk' => $totalRhk, 'totalIndikator' => $totalIndikator, 'filledIndikator' => $filledIndikator, 'persenTerisi' => $persenTerisi,
            'months' => [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember']
        ]);
    }
}