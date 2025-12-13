<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RealisasiKinerja;
use App\Models\PkAnggaran;
use App\Models\PerjanjianKinerja;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Dashboard extends Component
{
    // Filter
    public $periode = 'RPJMD 2021-2026';
    public $perangkat_daerah = '';

    // --- Modal State ---
    public $isOpenHighlight = false;
    public $activeTab = 'performer'; // performer, isu, dokumen

    // --- Data Detail untuk Modal ---
    public $detailPerformers = [];
    public $detailIsuKritis = [];
    public $detailDokumen = [];

    // Helper Format Angka
    private function formatShortNumber($num)
    {
        if ($num >= 1000000000000) return round($num / 1000000000000, 2) . 'T';
        if ($num >= 1000000000) return round($num / 1000000000, 2) . 'M';
        if ($num >= 1000000) return round($num / 1000000, 2) . 'Jt';
        return number_format($num, 0, ',', '.');
    }

    // --- Actions ---
    public function openHighlightModal($tab = 'performer')
    {
        $this->activeTab = $tab;
        $this->isOpenHighlight = true;
        
        // Load data detail (Lazy Loading)
        $this->loadDetailData();
    }

    public function closeHighlightModal()
    {
        $this->isOpenHighlight = false;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function loadDetailData()
    {
        // 1. Data Detail Performer & Isu
        $rawPerformance = DB::table('realisasi_kinerjas')
            ->join('pk_indikators', 'realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->select(
                'jabatans.nama as jabatan',
                'pk_indikators.nama_indikator',
                'realisasi_kinerjas.realisasi',
                'realisasi_kinerjas.capaian as capaian_db',
                'realisasi_kinerjas.tahun',
                DB::raw("CASE 
                    WHEN realisasi_kinerjas.tahun = 2025 THEN pk_indikators.target_2025
                    WHEN realisasi_kinerjas.tahun = 2026 THEN pk_indikators.target_2026
                    ELSE pk_indikators.target_2025 
                END as target_tahun")
            )
            ->get();

        // Proses Agregasi
        $tempScores = [];
        $this->detailIsuKritis = [];

        foreach ($rawPerformance as $row) {
            if ($row->target_tahun > 0) {
                $rawCapaian = ($row->realisasi / $row->target_tahun) * 100;
                $cappedCapaian = $rawCapaian > 100 ? 100 : $rawCapaian;

                // Grouping Jabatan
                if (!isset($tempScores[$row->jabatan])) {
                    $tempScores[$row->jabatan] = ['total' => 0, 'count' => 0];
                }
                $tempScores[$row->jabatan]['total'] += $cappedCapaian;
                $tempScores[$row->jabatan]['count']++;

                // Filter Isu Kritis
                if ($rawCapaian < 50) {
                    $this->detailIsuKritis[] = [
                        'jabatan' => $row->jabatan,
                        'indikator' => $row->nama_indikator,
                        'target' => $row->target_tahun,
                        'realisasi' => $row->realisasi,
                        'capaian' => round($rawCapaian, 1)
                    ];
                }
            }
        }

        // Finalisasi Data Performer (Ranking)
        $this->detailPerformers = [];
        foreach ($tempScores as $jabatan => $data) {
            if ($data['count'] > 0) {
                $score = $data['total'] / $data['count'];
                $this->detailPerformers[] = [
                    'jabatan' => $jabatan,
                    'score' => round($score, 1),
                    'jumlah_indikator' => $data['count']
                ];
            }
        }
        // Sort Ranking
        usort($this->detailPerformers, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // 2. Data Detail Dokumen
        $this->detailDokumen = PerjanjianKinerja::with(['jabatan.pegawai'])
            ->whereIn('status', ['draft', 'final'])
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->orderBy('jabatans.level', 'asc')
            ->orderBy('jabatans.id', 'asc')
            ->select('perjanjian_kinerjas.*')
            ->get()
            ->map(function($pk) {
                
                // Nama Pejabat
                $namaPejabat = $pk->jabatan->pegawai->nama ?? '-';
                $statusPejabat = $pk->jabatan->pegawai->status ?? '';
                
                if(in_array($statusPejabat, ['Plt', 'Pj', 'Pjs'])) {
                    $namaPejabat .= " ({$statusPejabat})";
                }

                return [
                    'jabatan' => $pk->jabatan->nama ?? '-',
                    'pegawai' => $namaPejabat,
                    'tahun' => $pk->tahun,
                    'status' => ucfirst($pk->status),
                    'tanggal' => $pk->updated_at->format('d M Y') // Tanggal Update Saja
                ];
            })
            ->toArray();
    }

    public function render()
    {
        // 1. QUERY & LOGIC: KINERJA
        $rawPerformance = DB::table('realisasi_kinerjas')
            ->join('pk_indikators', 'realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->select(
                'jabatans.nama as jabatan',
                'pk_indikators.nama_indikator',
                'realisasi_kinerjas.realisasi',
                'realisasi_kinerjas.tahun',
                DB::raw("CASE 
                    WHEN realisasi_kinerjas.tahun = 2025 THEN pk_indikators.target_2025
                    WHEN realisasi_kinerjas.tahun = 2026 THEN pk_indikators.target_2026
                    ELSE pk_indikators.target_2025 
                END as target_tahun")
            )
            ->get();

        $jabatanScores = [];
        $totalGlobalCapaian = 0;
        $countGlobalData = 0;
        
        $isuKritisCount = 0;
        $isuKritisNames = [];

        foreach ($rawPerformance as $row) {
            if ($row->target_tahun > 0) {
                $rawCapaian = ($row->realisasi / $row->target_tahun) * 100;
                $cappedCapaian = $rawCapaian > 100 ? 100 : $rawCapaian;
                
                $totalGlobalCapaian += $cappedCapaian;
                $countGlobalData++;

                if (!isset($jabatanScores[$row->jabatan])) {
                    $jabatanScores[$row->jabatan] = ['total' => 0, 'count' => 0];
                }
                $jabatanScores[$row->jabatan]['total'] += $cappedCapaian;
                $jabatanScores[$row->jabatan]['count']++;

                if ($rawCapaian < 50) {
                    $isuKritisCount++;
                    if (count($isuKritisNames) < 3) {
                        $isuKritisNames[] = Str::limit($row->nama_indikator, 20);
                    }
                }
            }
        }

        $avgCapaian = $countGlobalData > 0 ? ($totalGlobalCapaian / $countGlobalData) : 0;

        $topPerformerName = 'Belum ada data';
        $topPerformerScore = 0;
        if (count($jabatanScores) > 0) {
            $finalScores = [];
            foreach ($jabatanScores as $nm => $d) {
                $finalScores[$nm] = $d['count'] > 0 ? ($d['total'] / $d['count']) : 0;
            }
            arsort($finalScores);
            $topPerformerName = array_key_first($finalScores);
            $topPerformerScore = round(reset($finalScores), 1);
        }

        $isuKritisDesc = 'Semua indikator aman.';
        if ($isuKritisCount > 0) {
            $listNames = implode(', ', $isuKritisNames);
            if ($isuKritisCount > count($isuKritisNames)) $listNames .= ', dll';
            $isuKritisDesc = "{$isuKritisCount} Isu: {$listNames}.";
        }

        // 2. TOTAL DOKUMEN
        $pkStats = PerjanjianKinerja::selectRaw("status, count(*) as total")
            ->whereIn('status', ['draft', 'final'])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $countFinal = $pkStats['final'] ?? 0;
        $countDraft = $pkStats['draft'] ?? 0;
        $totalPk = $countFinal + $countDraft; 
        $totalPkDesc = "{$countFinal} Final & {$countDraft} Draft terdata.";

        // 3. LAINNYA
        $totalPaguRaw = PkAnggaran::sum('anggaran');
        $serapanRaw = $totalPaguRaw * ($avgCapaian / 100);

        $chartData = RealisasiKinerja::selectRaw('bulan, AVG(capaian) as rata_rata')
            ->where('tahun', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('rata_rata', 'bulan')
            ->toArray();
        
        $normalizedChart = [];
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 1; $i <= 12; $i++) {
            $normalizedChart[] = isset($chartData[$i]) ? round($chartData[$i], 1) : 0;
        }

        $activities = RealisasiKinerja::latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                $isCompleted = $item->capaian >= 100;
                return [
                    'waktu' => $item->created_at->diffForHumans(),
                    'aktivitas' => 'Update Realisasi', 
                    'user' => 'Pegawai', 
                    'status' => $isCompleted ? 'Tercapai' : 'Proses',
                    'status_color' => $isCompleted ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'
                ];
            });

        $data = [
            'stats' => [
                'capaian_rpjmd' => round($avgCapaian, 1),
                'renstra_sinkron' => $totalPk,
                'renstra_badge' => 'Total Dokumen PK',
                'serapan_anggaran' => 'Rp ' . $this->formatShortNumber($serapanRaw),
                'pagu_anggaran' => $this->formatShortNumber($totalPaguRaw),
                'isu_kritis' => $isuKritisCount,
            ],
            'highlights' => [
                [
                    'label' => 'Top Performer',
                    'desc' => $topPerformerName == 'Belum ada data' 
                                ? 'Menunggu data masuk' 
                                : "$topPerformerName ($topPerformerScore%)",
                    'icon' => 'star',
                    'color' => 'text-yellow-500'
                ],
                [
                    'label' => 'Perlu Perhatian',
                    'desc' => $isuKritisDesc,
                    'icon' => 'warning',
                    'color' => 'text-rose-500'
                ],
                [
                    'label' => 'Total Dokumen',
                    'desc' => $totalPkDesc,
                    'icon' => 'share',
                    'color' => 'text-blue-500'
                ],
            ],
            'activities' => $activities,
            'chart_data' => $normalizedChart,
            'chart_labels' => $bulanLabels
        ];

        return view('livewire.admin.dashboard', $data);
    }
}