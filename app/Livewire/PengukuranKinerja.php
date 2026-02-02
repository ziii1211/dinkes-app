<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja;
use App\Models\RencanaAksi;
use App\Models\RealisasiKinerja;
use App\Models\RealisasiRencanaAksi;
use App\Models\JadwalPengukuran;
use App\Models\PenjelasanKinerja;
use App\Models\Pegawai; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKinerjaBulananExport;

class PengukuranKinerja extends Component
{
    public $jabatan;
    public $pegawai;

    // Filter
    public $tahun;
    public $selectedMonth;
    public $availableYears = [];

    // Data Utama
    public $pk = null;
    public $rencanaAksis = [];
    public $penjelasans = [];
    
    // ID untuk Edit Penjelasan
    public $penjelasanId = null; 

    // Form Input Penjelasan (3 Field Manual)
    public $formUpaya;
    public $formHambatan;
    public $formRtl;

    // Status Jadwal
    public $isScheduleOpen = false;
    public $deadlineDate = null;
    public $scheduleMessage = '';

    // --- ACCESS CONTROL STATES ---
    public $canEdit = false;     
    public $canComment = false;  

    // --- MODAL STATES ---
    public $isOpenRealisasi = false;
    public $isOpenRealisasiAksi = false;
    public $isOpenTambahAksi = false;
    public $isOpenTambahPenjelasan = false;
    public $isOpenTanggapan = false;
    public $isOpenAturJadwal = false;

    // Form Inputs Lainnya
    public $formJadwalMulai, $formJadwalSelesai;
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

        $lastPk = PerjanjianKinerja::where('jabatan_id', $this->jabatan->id)
            ->where('status_verifikasi', 'disetujui')
            ->latest('tahun')
            ->first();

        // Ambil tahun dari URL (request get) jika ada
        $defaultTahun = $lastPk ? $lastPk->tahun : date('Y');
        $this->tahun = request()->query('tahun', $defaultTahun);
        
        // --- LOGIKA DEFAULT BULAN ---
        $now = Carbon::now();
        $activeSchedule = JadwalPengukuran::where('tahun', $this->tahun)
            ->where('is_active', true)
            ->whereDate('tanggal_mulai', '<=', $now)
            ->whereDate('tanggal_selesai', '>=', $now)
            ->first();

        if ($activeSchedule) {
            $this->selectedMonth = $activeSchedule->bulan;
        } else {
            $this->selectedMonth = (int) date('n');
        }

        $currentYear = date('Y');
        $this->availableYears = range($currentYear - 2, $currentYear + 5);

        // 1. Jalankan Cek Akses Awal
        $this->checkAccess();

        // 2. Load Data
        $this->loadData();
    }

    private function checkAccess()
    {
        $user = Auth::user();
        $this->canEdit = false;
        $this->canComment = false;

        // 1. ADMIN
        if ($user->role === 'admin') {
            $this->canEdit = true;
            $this->canComment = true;
            return;
        }

        $currentUserPegawai = Pegawai::where('nip', $user->nip)->first();
        $currentUserJabatanId = $currentUserPegawai ? $currentUserPegawai->jabatan_id : null;

        // 2. PEGAWAI
        if ($user->role === 'pegawai') {
            if ($this->pegawai && $user->nip === $this->pegawai->nip) {
                $this->canEdit = true; 
            }
        }

        // 3. PIMPINAN
        if ($user->role === 'pimpinan' && $currentUserJabatanId) {
            if ($this->jabatan->parent_id == $currentUserJabatanId) {
                $this->canComment = true;
            }
            if ($this->jabatan->id == $currentUserJabatanId && is_null($this->jabatan->parent_id)) {
                $this->canComment = true;
            }
        }
    }

    public function setTahun($year)
    {
        $this->tahun = $year;
        return redirect()->route('pengukuran.detail', [
            'jabatanId' => $this->jabatan->id,
            'tahun' => $year
        ]);
    }

    public function selectMonth($month)
    {
        $this->selectedMonth = $month;
        $this->loadData();
    }

    private function parseNumber($value)
    {
        if (is_null($value)) return 0;
        return (float) str_replace(',', '.', (string) $value);
    }

    /**
     * LOGIKA UTAMA: Mengambil Data PK, Rencana Aksi & Realisasi
     */
    public function loadData()
    {
        $this->checkScheduleStatus();

        // 1. Ambil PK (Filter berdasarkan kolom bulan yang dipilih)
        $this->pk = PerjanjianKinerja::with(['sasarans.indikators'])
            ->where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->tahun)
            ->where('status_verifikasi', 'disetujui')
            ->where('bulan', $this->selectedMonth) // Filter Bulan
            ->latest('id')
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

                    if ($data && $data->capaian !== null) {
                        $indikator->capaian_bulan = number_format($data->capaian, 2, ',', '.') . '%';
                    } elseif ($indikator->realisasi_bulan !== null) {
                        $target = $this->parseNumber($indikator->target_tahunan);
                        $realisasi = $this->parseNumber($indikator->realisasi_bulan);

                        if ($target > 0) {
                            $arah = strtolower(trim($indikator->arah ?? ''));
                            $isNegative = in_array($arah, ['menurun', 'turun', 'negative', 'negatif', 'min']);

                            if ($isNegative) {
                                $capaian = ((2 * $target) - $realisasi) / $target * 100;
                            } else {
                                $capaian = ($realisasi / $target) * 100;
                            }

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

        // 2. Ambil Rencana Aksi (Filter by Bulan & Tahun)
        $this->rencanaAksis = RencanaAksi::where('jabatan_id', $this->jabatan->id)
            ->where('tahun', $this->tahun)
            ->where('bulan', $this->selectedMonth) // <--- PERBAIKAN: Filter Bulan
            ->get();

        $realisasiAksiMap = RealisasiRencanaAksi::whereIn('rencana_aksi_id', $this->rencanaAksis->pluck('id'))
            ->where('bulan', $this->selectedMonth)
            ->where('tahun', $this->tahun)
            ->get()
            ->keyBy('rencana_aksi_id');

        foreach ($this->rencanaAksis as $aksi) {
            $dataAksi = $realisasiAksiMap->get($aksi->id);
            $aksi->realisasi_bulan = $dataAksi ? $dataAksi->realisasi : null;

            $targetAksi = $this->parseNumber($aksi->target);
            $realisasiAksi = $this->parseNumber($aksi->realisasi_bulan);

            if ($aksi->realisasi_bulan !== null && $targetAksi > 0) {
                $capaian = ($realisasiAksi / $targetAksi) * 100;
                if ($capaian > 100) $capaian = 100;
                if ($capaian < 0) $capaian = 0;
                $aksi->capaian_bulan = round($capaian);
            } else {
                $aksi->capaian_bulan = null;
            }
        }

        // 3. Ambil Penjelasan Kinerja
        if (class_exists(PenjelasanKinerja::class)) {
            $this->penjelasans = PenjelasanKinerja::where('jabatan_id', $this->jabatan->id)
                ->where('bulan', $this->selectedMonth)
                ->where('tahun', $this->tahun)
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $this->penjelasans = collect([]);
        }
    }

    // --- MANAJEMEN PENJELASAN KINERJA ---
    public function openTambahPenjelasan()
    {
        if (!$this->canEdit) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }
        $this->reset(['formUpaya', 'formHambatan', 'formRtl', 'penjelasanId']);
        $this->isOpenTambahPenjelasan = true;
    }

    public function editPenjelasan($id)
    {
        if (!$this->canEdit) return;

        $item = PenjelasanKinerja::find($id);
        if ($item && $item->jabatan_id == $this->jabatan->id) {
            $this->penjelasanId = $item->id;
            $this->formUpaya = $item->upaya;
            $this->formHambatan = $item->hambatan;
            $this->formRtl = $item->tindak_lanjut;
            $this->isOpenTambahPenjelasan = true;
        }
    }

    public function closeTambahPenjelasan()
    {
        $this->isOpenTambahPenjelasan = false;
    }

    public function simpanPenjelasan()
    {
        if (!$this->canEdit) return;

        $this->validate([
            'formUpaya' => 'nullable|string',
            'formHambatan' => 'nullable|string',
            'formRtl' => 'nullable|string',
        ]);

        PenjelasanKinerja::updateOrCreate(
            ['id' => $this->penjelasanId],
            [
                'jabatan_id' => $this->jabatan->id,
                'bulan' => $this->selectedMonth,
                'tahun' => $this->tahun,
                'upaya' => $this->formUpaya,
                'hambatan' => $this->formHambatan,
                'tindak_lanjut' => $this->formRtl,
            ]
        );

        $this->closeTambahPenjelasan();
        session()->flash('message', 'Penjelasan kinerja berhasil disimpan.');
        return redirect(request()->header('Referer'));
    }

    public function hapusPenjelasan($id)
    {
        if (!$this->canEdit) return;

        $item = PenjelasanKinerja::find($id);
        if ($item && $item->jabatan_id == $this->jabatan->id) {
            $item->delete();
            session()->flash('message', 'Penjelasan dihapus.');
        }
        return redirect(request()->header('Referer'));
    }

    // --- DOWNLOAD EXCEL ---
    public function downloadExcel()
    {
        $this->loadData();
        $bulan = $this->selectedMonth;
        $tahun = $this->tahun;
        $namaJabatanClean = str_replace(['/', '\\', ' '], '_', $this->jabatan->nama);
        $namaBulan = Carbon::create()->month($bulan)->locale('id')->translatedFormat('F');
        $namaFile = 'Laporan_Kinerja_' . $namaJabatanClean . '_' . $namaBulan . '_' . $tahun . '.xlsx';

        return Excel::download(new LaporanKinerjaBulananExport(
            $this->jabatan->id, 
            $bulan, 
            $tahun
        ), $namaFile);
    }

    // --- STATUS JADWAL ---
    public function checkScheduleStatus()
    {
        $isAdmin = Auth::user()->role === 'admin';

        if (!class_exists(JadwalPengukuran::class)) {
            if ($isAdmin) {
                $this->isScheduleOpen = true;
                $this->scheduleMessage = "Mode Admin: Akses penuh (Bypass jadwal).";
            }
            return;
        }

        $now = Carbon::now();
        $activeSchedule = JadwalPengukuran::where('tahun', $this->tahun)
            ->where('is_active', true)
            ->whereDate('tanggal_mulai', '<=', $now)
            ->whereDate('tanggal_selesai', '>=', $now)
            ->orderBy('tanggal_selesai', 'desc')
            ->first();

        if ($activeSchedule) {
            $this->isScheduleOpen = true;
            $end = Carbon::parse($activeSchedule->tanggal_selesai)->endOfDay();
            $this->deadlineDate = $end->translatedFormat('d F Y H:i') . ' WITA';
            $diff = $now->diff($end);
            $sisaWaktu = ($diff->days > 0) 
                    ? "{$diff->days} Hari {$diff->h} Jam lagi" 
                    : "{$diff->h} Jam {$diff->i} Menit lagi";
            $this->scheduleMessage = "Jadwal Pengisian Terbuka (Sisa: {$sisaWaktu}).";
        } else {
            $expiredSchedule = JadwalPengukuran::where('tahun', $this->tahun)
                ->where('bulan', $this->selectedMonth)
                ->whereDate('tanggal_selesai', '<', $now)
                ->first();

            if ($isAdmin) {
                $this->isScheduleOpen = true;
                $this->scheduleMessage = "Mode Admin: Akses penuh.";
            } else {
                $this->isScheduleOpen = false;
                if ($expiredSchedule) {
                    $end = Carbon::parse($expiredSchedule->tanggal_selesai)->endOfDay();
                    $this->deadlineDate = $end->translatedFormat('d F Y H:i') . ' WITA';
                    $this->scheduleMessage = "Batas waktu untuk bulan ini telah berakhir pada {$this->deadlineDate}.";
                } else {
                    $this->deadlineDate = '-';
                    $this->scheduleMessage = "Tidak ada Jadwal Pengisian yang aktif saat ini.";
                }
            }
        }

        if (!$this->isScheduleOpen && !$isAdmin) {
            $this->canEdit = false;
        }
    }

    // --- MODAL JADWAL ---
    public function openAturJadwal()
    {
        if (Auth::user()->role !== 'admin') return;

        $jadwal = JadwalPengukuran::where('tahun', $this->tahun)->where('bulan', $this->selectedMonth)->first();
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
            ['tanggal_mulai' => $this->formJadwalMulai, 'tanggal_selesai' => $this->formJadwalSelesai, 'is_active' => true]
        );
        $this->closeAturJadwal();
        session()->flash('message', 'Jadwal pengisian berhasil diperbarui.');
        return redirect(request()->header('Referer'));
    }

    // --- REALISASI INDIKATOR ---
    public function openRealisasi($id, $nama, $target, $satuan, $arah = '')
    {
        if (!$this->canEdit) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Anda tidak memiliki akses edit (Jadwal tutup atau bukan data Anda).']);
            return;
        }
        $this->indikatorId = $id;
        $this->indikatorNama = $nama;
        $this->indikatorTarget = $target;
        $this->indikatorSatuan = $satuan;
        $arahClean = strtolower(trim($arah));
        $this->showCapaianInput = in_array($arahClean, ['menurun', 'turun', 'negative', 'negatif', 'min']);
        $data = RealisasiKinerja::where('indikator_id', $id)->where('bulan', $this->selectedMonth)->where('tahun', $this->tahun)->first();
        $this->realisasiInput = $data ? $data->realisasi : '';
        $this->capaianInput = $data && $data->capaian !== null ? str_replace('.', ',', $data->capaian) : '';
        $this->catatanInput = $data ? $data->catatan : '';
        $this->isOpenRealisasi = true;
    }

    public function closeRealisasi()
    {
        $this->isOpenRealisasi = false;
    }

    public function simpanRealisasi()
    {
        if (!$this->canEdit) return;

        $this->validate(['realisasiInput' => ['required', 'regex:/^\d+([.,]\d+)?$/']]);
        $cleanRealisasi = str_replace(',', '.', $this->realisasiInput);
        $cleanCapaian = null;
        if ($this->showCapaianInput && $this->capaianInput !== '' && $this->capaianInput !== null) {
            $cleanCapaian = str_replace(',', '.', $this->capaianInput);
        }
        RealisasiKinerja::updateOrCreate(
            ['indikator_id' => $this->indikatorId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun],
            ['realisasi' => $cleanRealisasi, 'capaian' => $cleanCapaian, 'catatan' => $this->catatanInput]
        );
        $this->closeRealisasi();
        session()->flash('message', 'Data realisasi berhasil disimpan.');
        return redirect(request()->header('Referer'));
    }

    // --- REALISASI RENCANA AKSI ---
    public function openRealisasiAksi($id)
    {
        if (!$this->canEdit) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }
        $this->aksiId = $id;
        $aksi = RencanaAksi::find($id);
        $this->aksiNama = $aksi->nama_aksi;
        $this->aksiTarget = $aksi->target;
        $this->aksiSatuan = $aksi->satuan;
        $data = RealisasiRencanaAksi::where('rencana_aksi_id', $id)->where('bulan', $this->selectedMonth)->where('tahun', $this->tahun)->first();
        $this->realisasiAksiInput = $data ? $data->realisasi : '';
        $this->isOpenRealisasiAksi = true;
    }

    public function closeRealisasiAksi()
    {
        $this->isOpenRealisasiAksi = false;
    }

    public function simpanRealisasiAksi()
    {
        if (!$this->canEdit) return;
        $this->validate(['realisasiAksiInput' => ['required', 'regex:/^\d+([.,]\d+)?$/']]);
        $cleanRealisasi = str_replace(',', '.', $this->realisasiAksiInput);
        RealisasiRencanaAksi::updateOrCreate(
            ['rencana_aksi_id' => $this->aksiId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun],
            ['realisasi' => $cleanRealisasi]
        );
        $this->closeRealisasiAksi();
        session()->flash('message', 'Realisasi aksi berhasil disimpan.');
        return redirect(request()->header('Referer'));
    }

    // --- RENCANA AKSI MANUAL (CRUD) ---
    public function openTambahAksi()
    {
        if (!$this->canEdit) return;
        $this->reset(['formAksiNama', 'formAksiTarget', 'formAksiSatuan']);
        $this->isOpenTambahAksi = true;
    }

    public function closeTambahAksi()
    {
        $this->isOpenTambahAksi = false;
    }

    public function storeRencanaAksi()
    {
        if (!$this->canEdit) return;
        
        $this->validate(['formAksiNama' => 'required', 'formAksiTarget' => 'required', 'formAksiSatuan' => 'required']);
        $cleanTarget = str_replace(',', '.', $this->formAksiTarget);
        
        RencanaAksi::create([
            'jabatan_id' => $this->jabatan->id,
            'tahun' => $this->tahun,
            'bulan' => $this->selectedMonth, // <--- PENTING: Simpan Bulan
            'nama_aksi' => $this->formAksiNama,
            'target' => $cleanTarget,
            'satuan' => $this->formAksiSatuan
        ]);

        $this->closeTambahAksi();
        session()->flash('message', 'Rencana aksi berhasil ditambahkan.');
        return redirect(request()->header('Referer'));
    }

    public function deleteRencanaAksi($id)
    {
        if (!$this->canEdit) return;
        $aksi = RencanaAksi::find($id);
        if ($aksi) {
            RealisasiRencanaAksi::where('rencana_aksi_id', $id)->delete();
            $aksi->delete();
            session()->flash('message', 'Rencana Aksi berhasil dihapus.');
        }
        return redirect(request()->header('Referer'));
    }

    // --- TANGGAPAN (PIMPINAN) ---
    public function openTanggapan($id, $nama)
    {
        if (!$this->canComment) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Anda tidak memiliki wewenang untuk menanggapi bawahan ini.']);
            return;
        }
        $this->indikatorId = $id;
        $this->indikatorNama = $nama;
        $data = RealisasiKinerja::where('indikator_id', $id)->where('bulan', $this->selectedMonth)->where('tahun', $this->tahun)->first();
        $this->tanggapanInput = $data ? $data->tanggapan : '';
        $this->isOpenTanggapan = true;
    }

    public function closeTanggapan()
    {
        $this->isOpenTanggapan = false;
    }

    public function simpanTanggapan()
    {
        if (!$this->canComment) return;
        RealisasiKinerja::updateOrCreate(
            ['indikator_id' => $this->indikatorId, 'bulan' => $this->selectedMonth, 'tahun' => $this->tahun],
            ['tanggapan' => $this->tanggapanInput]
        );
        $this->closeTanggapan();
        session()->flash('message', 'Tanggapan berhasil disimpan.');
        return redirect(request()->header('Referer'));
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
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ]
        ]);
    }
}