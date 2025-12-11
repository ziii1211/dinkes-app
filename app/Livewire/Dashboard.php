<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RealisasiKinerja;
use App\Models\PkAnggaran;
use App\Models\PerjanjianKinerja;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Filter
    public $periode = 'RPJMD 2021-2026';
    public $perangkat_daerah = '';

    /**
     * Helper format angka singkatan (1.2M, 2T)
     */
    private function formatShortNumber($num)
    {
        if ($num >= 1000000000000) {
            return round($num / 1000000000000, 2) . 'T';
        } elseif ($num >= 1000000000) {
            return round($num / 1000000000, 2) . 'M';
        } elseif ($num >= 1000000) {
            return round($num / 1000000, 2) . 'Jt';
        }
        return number_format($num, 0, ',', '.');
    }

    public function render()
    {
        // 1. STATISTIK UTAMA
        $avgCapaian = RealisasiKinerja::avg('capaian') ?? 0;
        $totalPk = PerjanjianKinerja::count();
        $totalPaguRaw = PkAnggaran::sum('anggaran');
        $serapanRaw = $totalPaguRaw * ($avgCapaian > 0 ? ($avgCapaian / 100) : 0);

        // Indikator Underperform (Unik)
        $isuKritisCount = RealisasiKinerja::where('capaian', '<', 50)
            ->distinct('indikator_id')
            ->count('indikator_id');

        // 2. DATA CHART
        $chartData = RealisasiKinerja::selectRaw('MONTH(created_at) as bulan, AVG(capaian) as rata_rata')
            ->whereYear('created_at', date('Y'))
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('rata_rata', 'bulan')
            ->toArray();
        
        $normalizedChart = [];
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        for ($i = 1; $i <= 12; $i++) {
            $normalizedChart[] = round($chartData[$i] ?? 0, 1);
        }

        // 3. LOG AKTIVITAS
        $activities = RealisasiKinerja::latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'waktu' => $item->created_at->diffForHumans(),
                    'aktivitas' => 'Update Realisasi Kinerja', 
                    'user' => 'Pegawai', 
                    'status' => $item->capaian >= 100 ? 'Tercapai' : 'Proses',
                    'status_color' => $item->capaian >= 100 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                ];
            });

        $data = [
            'stats' => [
                'capaian_rpjmd' => round($avgCapaian, 1),
                'renstra_sinkron' => $totalPk,
                'renstra_badge' => 'Dokumen PK',
                'serapan_anggaran' => 'Rp ' . $this->formatShortNumber($serapanRaw),
                'pagu_anggaran' => $this->formatShortNumber($totalPaguRaw),
                'isu_kritis' => $isuKritisCount,
            ],
            'highlights' => [
                [
                    'label' => 'Top Performer',
                    'desc' => 'Capaian organisasi memuaskan.',
                    'icon' => 'star',
                    'color' => 'text-yellow-500'
                ],
                [
                    'label' => 'Perlu Perhatian',
                    'desc' => $isuKritisCount . ' indikator kinerja masih rendah.',
                    'icon' => 'warning',
                    'color' => 'text-red-500'
                ],
                [
                    'label' => 'Total Dokumen',
                    'desc' => $totalPk . ' Perjanjian Kinerja terdata.',
                    'icon' => 'share',
                    'color' => 'text-blue-500'
                ],
            ],
            'activities' => $activities,
            'chart_data' => $normalizedChart,
            'chart_labels' => $bulanLabels
        ];

        return view('livewire.dashboard', $data);
    }
}