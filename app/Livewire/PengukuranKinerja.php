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
    public $indikatorId, $indikatorNama, $indikatorTarget, $indikatorSatuan, $realisasiInput, $catatanInput;
    public $aksiId, $aksiNama, $aksiTarget, $aksiSatuan, $realisasiAksiInput;
    public $formAksiNama, $formAksiTarget, $formAksiSatuan;
    public $tanggapanInput;
    
    public function mount($jabatanId)
    {
        $this->jabatan = Jabatan::with('pegawai')->findOrFail($jabatanId);
        $this->pegawai = $this->jabatan->pegawai;

        // PERBAIKAN: Ambil PK terakhir yang status_verifikasi = 'disetujui'
        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
                    ->where('status_verifikasi', 'disetujui') // UPDATE DISINI
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
        $this->checkScheduleStatus();

        // PERBAIKAN: Ambil PK yang status_verifikasi = 'disetujui'
        $this->pk = PerjanjianKinerja::with(['sasarans.indikators'])
            ->where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->tahun)
            ->where('status_verifikasi', 'disetujui') // UPDATE DISINI
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

                    if ($indikator->realisasi_bulan !== null && $indikator->target_tahunan > 0) {
                        $capaian = ($indikator->realisasi_bulan / $indikator->target_tahunan) * 100;
                        $indikator->capaian_bulan = number_format($capaian, 2) . '%';
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
            if ($aksi->realisasi_bulan !== null && $aksi->target > 0) {
                $capaian = ($aksi->realisasi_bulan / $aksi->target) * 100;
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

    // --- FITUR ADMIN: ATUR JADWAL ---
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

    public function openRealisasi($id, $nama, $target, $satuan) {
        $this->indikatorId = $id; $this->indikatorNama = $nama; $this->indikatorTarget = $target; $this->indikatorSatuan = $satuan;
        $data = RealisasiKinerja::where('indikator_id', $id)->where('bulan', $this->selectedMonth)->where('tahun', $this->tahun)->first();
        $this->realisasiInput = $data ? $data->realisasi : ''; $this->catatanInput = $data ? $data->catatan : '';
        $this->isOpenRealisasi = true;
    }
    public function closeRealisasi() { $this->isOpenRealisasi = false; }
    public function simpanRealisasi() {
        $this->validate(['realisasiInput' => 'required|numeric']);
        RealisasiKinerja::updateOrCreate(['indikator_id' => $this->indikatorId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun], ['realisasi' => $this->realisasiInput, 'catatan' => $this->catatanInput]);
        $this->closeRealisasi(); $this->loadData();
    }
    public function openRealisasiAksi($id) {
        $this->aksiId = $id; $aksi = RencanaAksi::find($id);
        $this->aksiNama = $aksi->nama_aksi; $this->aksiTarget = $aksi->target; $this->aksiSatuan = $aksi->satuan;
        $data = RealisasiRencanaAksi::where('rencana_aksi_id', $id)->where('bulan', $this->selectedMonth)->where('tahun', $this->tahun)->first();
        $this->realisasiAksiInput = $data ? $data->realisasi : ''; $this->isOpenRealisasiAksi = true;
    }
    public function closeRealisasiAksi() { $this->isOpenRealisasiAksi = false; }
    public function simpanRealisasiAksi() {
        $this->validate(['realisasiAksiInput' => 'required|numeric']);
        RealisasiRencanaAksi::updateOrCreate(['rencana_aksi_id' => $this->aksiId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun], ['realisasi' => $this->realisasiAksiInput]);
        $this->closeRealisasiAksi(); $this->loadData();
    }
    public function openTambahAksi() { $this->reset(['formAksiNama', 'formAksiTarget', 'formAksiSatuan']); $this->isOpenTambahAksi = true; }
    public function closeTambahAksi() { $this->isOpenTambahAksi = false; }
    public function storeRencanaAksi() {
        $this->validate(['formAksiNama' => 'required', 'formAksiTarget' => 'required|numeric', 'formAksiSatuan' => 'required']);
        RencanaAksi::create(['jabatan_id' => $this->jabatan->id, 'tahun' => $this->tahun, 'nama_aksi' => $this->formAksiNama, 'target' => $this->formAksiTarget, 'satuan' => $this->formAksiSatuan]);
        $this->closeTambahAksi(); $this->loadData();
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