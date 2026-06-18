<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PerjanjianKinerja;
use App\Models\Jabatan;
use Illuminate\Support\Facades\DB; 

class PusatLaporan extends Component
{
    // Modal State
    public $showPkModal = false;
    public $showAksiModal = false;
    public $showBulananModal = false;
    public $showTahunanModal = false;
    public $showEmonevModal = false;
    public $showPegawaiModal = false; 
    public $showTopPerformerModal = false;
    
    // --- TAMBAHAN BARU: Modal State Laporan Penilaian Divisi ---
    public $showPenilaianDivisiModal = false; 
    
    public $pkList = [];
    public $bulanTerpilih = 1; 

    // State Data E-Monev & Pegawai
    public $listJabatan = [];
    public $emonevTahun;
    public $emonevJabatan = '';
    public $pegawaiJabatan = ''; 
    
    // --- TAMBAHAN BARU: State Data Penilaian Divisi ---
    public $penilaianTahun;
    public $penilaianJabatan = '';

    // State Data Top Performer 
    public $topTahun;
    public $topJabatan = '';
    public $topPerformerName = null;
    public $topPerformerScore = 0;
    public $alasanTopPerformer = null;

    public $showKeputusanKadisModal = false;
    public $keputusanKadisTahun;
    public $keputusanKadisBulan;

    // --- TAMBAHAN BARU: State Modal Laporan Grafik ---
    public $showGrafikModal = false;
    public $grafikTahun;
    public $grafikBulan;


    public function openPkModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->showPkModal = true;
    }
    public function closePkModal() { $this->showPkModal = false; }

    public function openAksiModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->showAksiModal = true;
    }
    public function closeAksiModal() { $this->showAksiModal = false; }

    public function openBulananModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->bulanTerpilih = date('n'); 
        $this->showBulananModal = true;
    }
    public function closeBulananModal() { $this->showBulananModal = false; }

    public function openTahunanModal() {
        $this->pkList = PerjanjianKinerja::with(['jabatan', 'pegawai'])->orderBy('tahun', 'desc')->get();
        $this->showTahunanModal = true;
    }
    public function closeTahunanModal() { $this->showTahunanModal = false; }

    public function openEmonevModal() {
        $this->listJabatan = Jabatan::orderBy('id', 'asc')->get();
        $this->emonevTahun = date('Y');
        $this->emonevJabatan = '';
        $this->showEmonevModal = true;
    }
    public function closeEmonevModal() { $this->showEmonevModal = false; }

    public function openPegawaiModal() {
        $this->listJabatan = Jabatan::orderBy('id', 'asc')->get();
        $this->pegawaiJabatan = ''; 
        $this->showPegawaiModal = true;
    }
    public function closePegawaiModal() { $this->showPegawaiModal = false; }

    // --- TAMBAHAN BARU: Fungsi Modal Laporan Penilaian Divisi ---
    public function openPenilaianDivisiModal() {
        $this->listJabatan = Jabatan::orderBy('id', 'asc')->get();
        $this->penilaianTahun = date('Y');
        $this->penilaianJabatan = ''; 
        $this->showPenilaianDivisiModal = true;
    }
    public function closePenilaianDivisiModal() { $this->showPenilaianDivisiModal = false; }

    public function generatePenilaianDivisi() {
        $this->validate([
            'penilaianJabatan' => 'required',
            'penilaianTahun' => 'required'
        ], [
            'penilaianJabatan.required' => 'Pilih Divisi/Jabatan terlebih dahulu!',
            'penilaianTahun.required' => 'Pilih Tahun terlebih dahulu!'
        ]);

        // Redirect langsung ke halaman cetak PDF
        return redirect()->route('cetak.penilaian-divisi', [
            'jabatan_id' => $this->penilaianJabatan, 
            'tahun' => $this->penilaianTahun
        ]);
    }

    // --- TAMBAHAN BARU: Fungsi Modal Keputusan Kadis ---
    public function openKeputusanKadisModal() {
        $this->keputusanKadisTahun = date('Y');
        $this->keputusanKadisBulan = date('n'); // <-- Default ke bulan saat ini (1-12)
        $this->showKeputusanKadisModal = true;
    }
    public function closeKeputusanKadisModal() { $this->showKeputusanKadisModal = false; }

    public function generateKeputusanKadis() {
        $this->validate([
            'keputusanKadisTahun' => 'required',
            'keputusanKadisBulan' => 'required' // <-- Tambahan validasi bulan
        ], [
            'keputusanKadisTahun.required' => 'Pilih Tahun terlebih dahulu!',
            'keputusanKadisBulan.required' => 'Pilih Bulan terlebih dahulu!'
        ]);

        return redirect()->route('cetak.keputusan-kadis', [
            'tahun' => $this->keputusanKadisTahun,
            'bulan' => $this->keputusanKadisBulan // <-- Kirim data bulan ke route
        ]);
    }

    // --- TAMBAHAN BARU: Fungsi Modal Laporan Grafik ---
    public function openGrafikModal() {
        $this->grafikTahun = date('Y');
        $this->grafikBulan = date('n');
        $this->showGrafikModal = true;
    }
    public function closeGrafikModal() { $this->showGrafikModal = false; }

    public function generateGrafik() {
        $this->validate([
            'grafikTahun' => 'required',
            'grafikBulan' => 'required'
        ]);

        // Langsung redirect ke route cetak dengan membawa data bulan dan tahun
        return redirect()->route('cetak.grafik', [
            'tahun' => $this->grafikTahun,
            'bulan' => $this->grafikBulan
        ]);
    }
    // --- MODAL TOP PERFORMER ---
    public function openTopPerformerModal() {
        $this->listJabatan = Jabatan::orderBy('id', 'asc')->get();
        $this->topTahun = date('Y');
        $this->topJabatan = ''; 
        $this->loadTopPerformerData(); // Hitung Data Saat Modal Dibuka
        $this->showTopPerformerModal = true;
    }
    public function closeTopPerformerModal() { $this->showTopPerformerModal = false; }

    // Otomatis update ketika filter tahun/jabatan diubah di view
    public function updatedTopTahun() {
        $this->loadTopPerformerData();
    }
    public function updatedTopJabatan() {
        $this->loadTopPerformerData();
    }

    // Fungsi Kalkulasi Top Performer (Replikasi dari Dashboard)
    public function loadTopPerformerData()
    {
        $query = DB::table('realisasi_kinerjas')
            ->join('pk_indikators', 'realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->where('realisasi_kinerjas.tahun', $this->topTahun);

        if ($this->topJabatan != '') {
            $query->where('jabatans.id', $this->topJabatan);
        }

        $rawPerformance = $query->select(
                'jabatans.nama as jabatan',
                'pk_indikators.nama_indikator',
                'pk_indikators.arah', 
                'realisasi_kinerjas.realisasi',
                'realisasi_kinerjas.tahun',
                DB::raw("CASE 
                    WHEN realisasi_kinerjas.tahun = 2025 THEN pk_indikators.target_2025
                    WHEN realisasi_kinerjas.tahun = 2026 THEN pk_indikators.target_2026
                    ELSE pk_indikators.target_2025 
                END as target_tahun")
            )->get();

        $jabatanScores = [];
        foreach ($rawPerformance as $row) {
            $target = (float) str_replace(',', '.', $row->target_tahun);
            $realisasi = (float) str_replace(',', '.', $row->realisasi);
            $arah = strtolower(trim($row->arah ?? ''));
            $isNegative = in_array($arah, ['menurun', 'turun', 'negative', 'negatif', 'min']);

            if ($target > 0) {
                if ($isNegative) {
                    $rawCapaian = ((2 * $target) - $realisasi) / $target * 100;
                } else {
                    $rawCapaian = ($realisasi / $target) * 100;
                }
                
                $cappedCapaian = $rawCapaian > 100 ? 100 : ($rawCapaian < 0 ? 0 : $rawCapaian);

                if (!isset($jabatanScores[$row->jabatan])) {
                    $jabatanScores[$row->jabatan] = ['total' => 0, 'count' => 0];
                }
                $jabatanScores[$row->jabatan]['total'] += $cappedCapaian;
                $jabatanScores[$row->jabatan]['count']++;
            }
        }

        $this->topPerformerName = null;
        $this->topPerformerScore = 0;
        $this->alasanTopPerformer = null;

        if (count($jabatanScores) > 0) {
            $finalScores = [];
            foreach ($jabatanScores as $nm => $d) {
                $finalScores[$nm] = $d['count'] > 0 ? ($d['total'] / $d['count']) : 0;
            }
            arsort($finalScores); // Urutkan yang tertinggi
            $this->topPerformerName = array_key_first($finalScores);
            $this->topPerformerScore = round(reset($finalScores), 2);
            
            // Susun Alasan Dinamis
            if ($this->topPerformerScore > 0) {
                $cakupan = $this->topJabatan != '' ? 'jabatan ini' : 'keseluruhan dinas';
                $this->alasanTopPerformer = "{$this->topPerformerName} ditetapkan sebagai Top Performer karena berhasil mencatatkan rata-rata capaian kinerja tertinggi sebesar {$this->topPerformerScore}% dibandingkan unit/jabatan lainnya pada cakupan {$cakupan} di periode evaluasi tahun {$this->topTahun}.";
            }
        }
    }

    public function render()
    {
        return view('livewire.pusat-laporan')
            ->layout('components.layouts.app', ['title' => 'Pusat Laporan']);
    }
}