<?php

namespace App\Livewire\Pimpinan;

use Livewire\Component;
use App\Models\RealisasiKinerja;
use App\Models\PkAnggaran;
use App\Models\PerjanjianKinerja;
use App\Models\JadwalPengukuran; 
use App\Models\Jabatan; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $periode = 'Renstra 2026-2030';
    public $perangkat_daerah = ''; 

    public $isOpenHighlight = false;
    public $activeTab = 'performer';

    public $detailPerformers = [];
    public $detailIsuKritis = [];
    public $detailDokumen = [];

    private function formatShortNumber($num)
    {
        if ($num >= 1000000000000) return round($num / 1000000000000, 2) . 'T';
        if ($num >= 1000000000) return round($num / 1000000000, 2) . 'M';
        if ($num >= 1000000) return round($num / 1000000, 2) . 'Jt';
        return number_format($num, 0, ',', '.');
    }

    public function openHighlightModal($tab = 'performer')
    {
        $this->activeTab = $tab;
        $this->isOpenHighlight = true;
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
        $rawPerformance = DB::table('realisasi_kinerjas')
            ->join('pk_indikators', 'realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->when($this->perangkat_daerah, function($query) {
                $query->where('jabatans.id', $this->perangkat_daerah);
            })
            ->select(
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
            )
            ->get();

        $tempScores = [];
        $this->detailIsuKritis = [];

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

                if (!isset($tempScores[$row->jabatan])) {
                    $tempScores[$row->jabatan] = ['total' => 0, 'count' => 0];
                }
                $tempScores[$row->jabatan]['total'] += $cappedCapaian;
                $tempScores[$row->jabatan]['count']++;

                if ($rawCapaian < 50) {
                    $this->detailIsuKritis[] = [
                        'jabatan' => $row->jabatan,
                        'indikator' => $row->nama_indikator,
                        'target' => $target,
                        'realisasi' => $realisasi,
                        'capaian' => round($rawCapaian, 1)
                    ];
                }
            }
        }

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
        usort($this->detailPerformers, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $this->detailDokumen = PerjanjianKinerja::with(['jabatan.pegawai'])
            ->whereIn('status', ['draft', 'final'])
            ->when($this->perangkat_daerah, function($query) {
                $query->where('jabatan_id', $this->perangkat_daerah);
            })
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->orderBy('jabatans.level', 'asc')
            ->orderBy('jabatans.id', 'asc')
            ->select('perjanjian_kinerjas.*')
            ->get()
            ->map(function($pk) {
                $namaPejabat = $pk->jabatan->pegawai->nama ?? '-';
                $statusPejabat = $pk->jabatan->pegawai->status ?? '';
                if(in_array($statusPejabat, ['Plt', 'Pj', 'Pjs'])) $namaPejabat .= " ({$statusPejabat})";

                return [
                    'jabatan' => $pk->jabatan->nama ?? '-',
                    'pegawai' => $namaPejabat,
                    'tahun' => $pk->tahun,
                    'status' => ucfirst($pk->status),
                    'tanggal' => $pk->updated_at->format('d M Y')
                ];
            })
            ->toArray();
    }

    public function render()
    {
        Carbon::setLocale('id'); 
        $jabatans = Jabatan::orderBy('nama', 'asc')->get();

        $rawPerformance = DB::table('realisasi_kinerjas')
            ->join('pk_indikators', 'realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->when($this->perangkat_daerah, function($query) {
                $query->where('jabatans.id', $this->perangkat_daerah);
            })
            ->select(
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
            )
            ->get();

        $jabatanScores = [];
        $totalGlobalCapaian = 0;
        $countGlobalData = 0;
        $isuKritisCount = 0;
        $isuKritisNames = [];

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

        $pkStats = PerjanjianKinerja::query()
            ->when($this->perangkat_daerah, function($query) {
                $query->where('jabatan_id', $this->perangkat_daerah);
            })
            ->selectRaw("status, count(*) as total")
            ->whereIn('status', ['draft', 'final'])
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $countFinal = $pkStats['final'] ?? 0;
        $countDraft = $pkStats['draft'] ?? 0;
        $totalPk = $countFinal + $countDraft; 
        $totalPkDesc = "{$countFinal} Final & {$countDraft} Draft terdata.";

        $totalPaguRaw = PkAnggaran::sum('anggaran');
        $serapanRaw = $totalPaguRaw * ($avgCapaian / 100);

        $chartData = RealisasiKinerja::selectRaw('bulan, AVG(capaian) as rata_rata')
            ->where('tahun', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('rata_rata', 'bulan')
            ->toArray();
        
        $normalizedChart = [];
        $hasRealChartData = false;
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        if(count($chartData) > 0) {
            $hasRealChartData = true;
            for ($i = 1; $i <= 12; $i++) {
                $normalizedChart[] = isset($chartData[$i]) ? round($chartData[$i], 1) : 0;
            }
        } else {
            $normalizedChart = [15, 25, 30, 42, 50, 58, 65, 75, 82, 88, 95, 100];
        }

        $activities = DB::table('realisasi_kinerjas')
            ->join('pk_indikators', 'realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->when($this->perangkat_daerah, function($query) {
                $query->where('jabatans.id', $this->perangkat_daerah);
            })
            ->leftJoin('pegawais', 'jabatans.id', '=', 'pegawais.jabatan_id')
            ->select(
                'realisasi_kinerjas.id',
                'realisasi_kinerjas.bulan',
                'realisasi_kinerjas.capaian',
                'realisasi_kinerjas.tanggapan',
                'realisasi_kinerjas.updated_at',
                'pk_indikators.nama_indikator',
                'pegawais.nama as nama_pegawai',
                'jabatans.nama as nama_jabatan'
            )
            ->orderBy('realisasi_kinerjas.updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $isFeedback = !empty($item->tanggapan);
                $statusText = 'Proses';
                $statusColor = 'bg-amber-100 text-amber-700';

                if ($isFeedback) {
                    $statusText = 'Ditanggapi';
                    $statusColor = 'bg-blue-100 text-blue-700';
                    $activityText = 'Memberikan Tanggapan <span class="text-slate-400 font-normal">pada indikator</span> ' . Str::limit($item->nama_indikator, 30);
                } else {
                    if ($item->capaian >= 100) {
                        $statusText = 'Tercapai';
                        $statusColor = 'bg-emerald-100 text-emerald-700';
                    }
                    $activityText = 'Melaporkan Kinerja <span class="font-bold text-indigo-600">Bulan ' . Carbon::createFromFormat('m', $item->bulan)->isoFormat('MMMM') . '</span>';
                }

                $userName = $item->nama_pegawai ?? $item->nama_jabatan;

                return [
                    'waktu' => Carbon::parse($item->updated_at)->diffForHumans(),
                    'aktivitas' => $activityText,
                    'user' => Str::limit($userName, 20),
                    'status' => $statusText,
                    'status_color' => $statusColor
                ];
            });

        $now = Carbon::now();
        $activeSchedule = JadwalPengukuran::where('is_active', true)
            ->whereDate('tanggal_mulai', '<=', $now)
            ->whereDate('tanggal_selesai', '>=', $now)
            ->first();

        $deadlineData = null;
        $sisaHari = 0;
        $bulanNama = '';

        if ($activeSchedule) {
            $end = Carbon::parse($activeSchedule->tanggal_selesai)->endOfDay();
            $sisaHari = (int) $now->diffInDays($end, false);
            if ($sisaHari < 0) $sisaHari = 0;

            $bulanList = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $bulanNama = $bulanList[$activeSchedule->bulan] ?? 'Bulan Ini';

            $deadlineData = $activeSchedule;
        }

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
            'chart_labels' => $bulanLabels,
            'is_dummy_chart' => !$hasRealChartData,
            
            'deadline' => $deadlineData,
            'sisa_hari' => $sisaHari,
            'bulan_nama' => $bulanNama,
            'jabatans' => $jabatans 
        ];

        return view('livewire.pimpinan.dashboard', $data);
    }
}          