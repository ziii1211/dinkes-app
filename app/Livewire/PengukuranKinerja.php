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
use App\Models\Pegawai; // PENTING: Import Model Pegawai
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// Tambahkan Import Ini untuk Fitur Export Excel
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKinerjaBulananExport;

class PengukuranKinerja extends Component
{
    public $jabatan;
    public $pegawai;

    public $tahun;
    public $selectedMonth;

    public $availableYears = [];

    public $pk = null;
    public $rencanaAksis = [];

    // Properti Penjelasan Kinerja (Tabel 3 Kolom)
    public $penjelasans = [];

    // Form Input Penjelasan (3 Field Manual)
    public $formUpaya;
    public $formHambatan;
    public $formRtl;

    // Status Jadwal
    public $isScheduleOpen = false;
    public $deadlineDate = null;
    public $scheduleMessage = '';

    // --- ACCESS CONTROL STATES ---
    public $canEdit = false;     // Untuk Pegawai (Input Realisasi) & Admin
    public $canComment = false;  // Untuk Pimpinan (Beri Tanggapan) & Admin

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

        // PERBAIKAN 1: Ambil tahun dari URL (request get) jika ada, untuk menghindari reset saat redirect
        $defaultTahun = $lastPk ? $lastPk->tahun : date('Y');
        $this->tahun = request()->query('tahun', $defaultTahun);
        
        $this->selectedMonth = (int) date('n');

        $currentYear = date('Y');
        $this->availableYears = range($currentYear - 2, $currentYear + 5);

        // 1. Jalankan Cek Akses Awal
        $this->checkAccess();

        // 2. Load Data
        $this->loadData();
    }

    /**
     * LOGIKA UTAMA OTORISASI (Authorization)
     */
    private function checkAccess()
    {
        $user = Auth::user();

        // Reset dulu ke false
        $this->canEdit = false;
        $this->canComment = false;

        // 1. ADMIN: Dewa (Bisa Edit Data & Bisa Komentar di mana saja)
        if ($user->role === 'admin') {
            $this->canEdit = true;
            $this->canComment = true;
            return;
        }

        // Cari Data Pegawai dari User yang sedang login
        // (Asumsi: User NIP sama dengan Pegawai NIP)
        $currentUserPegawai = Pegawai::where('nip', $user->nip)->first();
        $currentUserJabatanId = $currentUserPegawai ? $currentUserPegawai->jabatan_id : null;

        // 2. PEGAWAI: Hanya bisa Edit data miliknya sendiri
        // Logika: NIP User Login == NIP Pegawai pemilik Jabatan yang sedang dibuka
        if ($this->pegawai && $user->nip === $this->pegawai->nip) {
            $this->canEdit = true; 
            // Pegawai tidak bisa komentar/menanggapi diri sendiri (opsional)
        }

        // 3. PIMPINAN: Hanya bisa Menanggapi Bawahan Langsung
        // Logika: Jabatan yang dibuka ($this->jabatan) adalah ANAK dari Jabatan User Login
        if ($user->role === 'pimpinan' && $currentUserJabatanId) {
            if ($this->jabatan->parent_id == $currentUserJabatanId) {
                $this->canComment = true; // Hanya boleh komentar, TIDAK boleh edit data bawahan
            }
        }
    }

    public function setTahun($year)
    {
        $this->tahun = $year;
        // PERBAIKAN 2: Gunakan redirect()->route() agar method tetap GET dan tidak error MethodNotAllowed
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

    public function loadData()
    {
        // Cek Jadwal dulu, karena ini bisa menganulir $canEdit
        $this->checkScheduleStatus();

        // 1. Ambil PK Utama
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

        // 2. Ambil Rencana Aksi
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
        // Proteksi Akses
        if (!$this->canEdit) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Akses ditolak.']);
            return;
        }

        $this->reset(['formUpaya', 'formHambatan', 'formRtl']);
        $this->isOpenTambahPenjelasan = true;
    }

    public function closeTambahPenjelasan()
    {
        $this->isOpenTambahPenjelasan = false;
    }

    public function simpanPenjelasan()
    {
        if (!$this->canEdit) return; // Proteksi Backend

        $this->validate([
            'formUpaya' => 'nullable|string',
            'formHambatan' => 'nullable|string',
            'formRtl' => 'nullable|string',
        ]);

        PenjelasanKinerja::create([
            'jabatan_id' => $this->jabatan->id,
            'bulan' => $this->selectedMonth,
            'tahun' => $this->tahun,
            'upaya' => $this->formUpaya,
            'hambatan' => $this->formHambatan,
            'tindak_lanjut' => $this->formRtl,
        ]);

        $this->closeTambahPenjelasan();
        session()->flash('message', 'Penjelasan kinerja berhasil ditambahkan.');
        return redirect(request()->header('Referer'));
    }

    public function hapusPenjelasan($id)
    {
        if (!$this->canEdit) return; // Proteksi Backend

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

        // 1. Cek Model
        if (!class_exists(JadwalPengukuran::class)) {
            if ($isAdmin) {
                $this->isScheduleOpen = true;
                $this->scheduleMessage = "Mode Admin: Akses penuh (Bypass jadwal).";
            }
            return;
        }

        // 2. Ambil Jadwal dari DB
        $jadwal = JadwalPengukuran::where('tahun', $this->tahun)
            ->where('bulan', $this->selectedMonth)
            ->first();

        $now = Carbon::now();

        // 3. Logika Penentuan Pesan & Status
        if ($jadwal && $jadwal->is_active) {
            // PERBAIKAN 3: Jika jadwal ada, Admin juga melihat hitung mundur (real time info)
            // Tapi Admin tetap bisa edit (isScheduleOpen = true) meskipun waktu habis.
            
            $start = Carbon::parse($jadwal->tanggal_mulai)->startOfDay();
            $end = Carbon::parse($jadwal->tanggal_selesai)->endOfDay();
            $this->deadlineDate = $end->translatedFormat('d F Y H:i') . ' WITA';

            if ($now->between($start, $end)) {
                // MASIH DALAM PERIODE
                $this->isScheduleOpen = true;
                $diff = $now->diff($end);
                $this->scheduleMessage = ($diff->days > 0) 
                    ? "Sisa Waktu: {$diff->days} Hari {$diff->h} Jam lagi." 
                    : "Segera Berakhir: {$diff->h} Jam {$diff->i} Menit lagi.";
            } else if ($now->gt($end)) {
                // SUDAH LEWAT TENGGAT
                // Admin tetap bisa akses (Open), User biasa terkunci (False)
                $this->isScheduleOpen = $isAdmin ? true : false;
                $this->scheduleMessage = "Batas waktu telah berakhir pada {$this->deadlineDate}.";
            } else {
                // BELUM MULAI
                // Admin tetap bisa akses (Open), User biasa terkunci (False)
                $this->isScheduleOpen = $isAdmin ? true : false;
                $this->scheduleMessage = "Jadwal belum dimulai. Dibuka pada " . $start->translatedFormat('d F Y');
            }
        } else {
            // JIKA JADWAL BELUM DIATUR/TIDAK ADA DI DB
            if ($isAdmin) {
                $this->isScheduleOpen = true;
                $this->scheduleMessage = "Mode Admin: Akses penuh (Bypass jadwal / Belum disetting).";
            } else {
                $this->isScheduleOpen = false;
                $this->deadlineDate = '-';
                $this->scheduleMessage = "Jadwal pengisian belum dibuka oleh Admin.";
            }
        }

        // Finalisasi: Pastikan Admin selalu bisa edit, 
        // User hanya bisa edit jika isScheduleOpen bernilai true.
        if (!$this->isScheduleOpen && !$isAdmin) {
            $this->canEdit = false;
        }
    }

    // --- MODAL JADWAL ---
    public function openAturJadwal()
    {
        // Hanya Admin
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
        // Proteksi
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
        if (!$this->canEdit) return; // Proteksi Backend

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
        if (!$this->canEdit) return; // Proteksi Backend

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

    // --- RENCANA AKSI MANUAL ---
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
        if (!$this->canEdit) return; // Proteksi Backend

        $this->validate(['formAksiNama' => 'required', 'formAksiTarget' => 'required', 'formAksiSatuan' => 'required']);
        $cleanTarget = str_replace(',', '.', $this->formAksiTarget);
        RencanaAksi::create([
            'jabatan_id' => $this->jabatan->id,
            'tahun' => $this->tahun,
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
        if (!$this->canEdit) return; // Proteksi Backend

        $aksi = RencanaAksi::find($id);
        if ($aksi) {
            RealisasiRencanaAksi::where('rencana_aksi_id', $id)->delete();
            $aksi->delete();
            session()->flash('message', 'Rencana Aksi berhasil dihapus.');
        }
        return redirect(request()->header('Referer'));
    }

    // --- TANGGAPAN (Fitur Khusus Pimpinan & Admin) ---
    public function openTanggapan($id, $nama)
    {
        // Gunakan checkAccess ($canComment)
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
        // Proteksi Backend
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