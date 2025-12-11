<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\RealisasiKinerja;
use App\Models\PkAnggaran;
use App\Models\PerjanjianKinerja;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Filter (Disiapkan untuk pengembangan selanjutnya)
    public $periode = 'RPJMD 2021-2026';
    public $perangkat_daerah = '';

    /**
     * Helper sederhana untuk format angka singkatan (Contoh: 1.5M, 2T)
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
        // 1. DATA STATISTIK
        
        // A. Rata-rata Capaian (Global)
        $avgCapaian = RealisasiKinerja::avg('capaian') ?? 0;

        // B. Dokumen PK (Total Dokumen Masuk)
        $totalPk = PerjanjianKinerja::count();
        
        // C. Estimasi Serapan & Pagu Anggaran
        $totalPaguRaw = PkAnggaran::sum('anggaran');
        // Rumus Estimasi: Jika kinerja 100%, serapan dianggap 100% dari pagu.
        $serapanRaw = $totalPaguRaw * ($avgCapaian / 100);

        // D. Indikator Underperform (Merah)
        // Menghitung jumlah indikator UNIK yang pernah lapor capaian < 50%
        // Menggunakan distinct agar jika indikator X merah di Jan & Feb, tetap dihitung 1 indikator.
        $isuKritisCount = RealisasiKinerja::where('capaian', '<', 50)
            ->distinct('indikator_id')
            ->count('indikator_id');

        // 2. DATA CHART (Tren Capaian Bulanan)
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

        // 3. LOG AKTIVITAS (Limit 3 teratas)
        $activities = RealisasiKinerja::latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'waktu' => $item->created_at->diffForHumans(),
                    'aktivitas' => 'Update Realisasi Kinerja', 
                    // Placeholder karena tabel user belum terelasi sempurna di migrasi
                    'user' => 'Pegawai', 
                    'status' => $item->capaian >= 100 ? 'Tercapai' : 'Proses',
                    'status_color' => $item->capaian >= 100 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                ];
            });

        // MENYUSUN DATA KE VIEW
        $data = [
            'stats' => [
                'capaian_rpjmd' => round($avgCapaian, 1),
                'renstra_sinkron' => $totalPk,
                'renstra_badge' => 'Dokumen PK',
                // Format Serapan (misal: Rp 1.42T)
                'serapan_anggaran' => 'Rp ' . $this->formatShortNumber($serapanRaw),
                // Format Pagu (misal: 2.1T)
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

        return view('livewire.admin.dashboard', $data);
    }
}