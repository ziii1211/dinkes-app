<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Dashboard extends Component
{
    public $periode = 'RPJMD 2021-2026';
    public $perangkat_daerah = '';

    public function render()
    {
        // DATA DUMMY (Sesuaikan dengan gambar untuk preview UI)
        $data = [
            'stats' => [
                'capaian_rpjmd' => 72,
                'renstra_sinkron' => '54/72',
                'renstra_badge' => '+3 minggu ini',
                'serapan_anggaran' => 'Rp 1,42T',
                'pagu_anggaran' => '2,10T',
                'isu_kritis' => 7,
            ],
            'highlights' => [
                [
                    'label' => 'Top Performer: Pendidikan',
                    'desc' => 'Capaian 88%, serapan 92%.',
                    'icon' => 'star',
                    'color' => 'text-yellow-500'
                ],
                [
                    'label' => 'Perlu Perhatian: Kesehatan',
                    'desc' => '3 indikator turun dibanding triwulan lalu.',
                    'icon' => 'warning',
                    'color' => 'text-red-500'
                ],
                [
                    'label' => 'Cascading SKPD',
                    'desc' => '54 SKPD telah sinkron.',
                    'icon' => 'share',
                    'color' => 'text-blue-500'
                ],
            ],
            'activities' => [
                [
                    'waktu' => 'Hari ini 09:12',
                    'aktivitas' => 'Update indikator <strong>Angka Partisipasi Sekolah</strong>',
                    'user' => 'Yudha',
                    'status' => 'Sukses',
                    'status_color' => 'bg-green-100 text-green-700'
                ],
                [
                    'waktu' => 'Kemarin 16:40',
                    'aktivitas' => 'Sinkronisasi Renstra Dinas Kesehatan',
                    'user' => 'Rina',
                    'status' => 'Proses',
                    'status_color' => 'bg-yellow-100 text-yellow-700'
                ],
                [
                    'waktu' => '3 hari lalu',
                    'aktivitas' => 'Import pagu triwulan III',
                    'user' => 'Dedi',
                    'status' => 'Gagal',
                    'status_color' => 'bg-red-100 text-red-700'
                ],
            ]
        ];

        return view('livewire.admin.dashboard', $data);
    }
}