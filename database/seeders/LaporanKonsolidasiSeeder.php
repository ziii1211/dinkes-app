<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LaporanKonsolidasi;
use App\Models\DetailLaporanKonsolidasi;
use App\Models\LaporanKonsolidasiAnggaran;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\SubKegiatan;
use App\Models\IndikatorSubKegiatan;

class LaporanKonsolidasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan pengecekan Foreign Key untuk pembersihan tabel laporan e-monev
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DetailLaporanKonsolidasi::truncate();
        LaporanKonsolidasiAnggaran::truncate();
        LaporanKonsolidasi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Siapkan data master Pagu & Target untuk Sub Kegiatan jika masih 0
        $subKegiatans = SubKegiatan::with(['kegiatan.program', 'indikators'])->get();

        if ($subKegiatans->isEmpty()) {
            $this->command?->warn("Tidak ada Sub Kegiatan di database. Silakan jalankan seeder program/kegiatan terlebih dahulu.");
            return;
        }

        // Mapping pagu realistis untuk Sub Kegiatan (berdasarkan ID atau nama)
        $defaultPagus = [
            21 => 1250000000, // Penyediaan Gaji dan Tunjangan ASN
            22 => 145000000,  // Penyediaan Administrasi Pelaksanaan Tugas ASN
            23 => 65000000,   // Koordinasi dan Penyusunan Laporan Keuangan Akhir Tahun SKPD
            24 => 45000000,   // Koordinasi dan Penyusunan Laporan Keuangan Bulanan/Triwulanan
            25 => 35000000,   // Penyusunan Pelaporan dan Analisis Prognosis
            26 => 50000000,   // Penyusunan RKBMD SKPD
            27 => 40000000,   // Rekonsiliasi dan Penyusunan Laporan BMD
            28 => 85000000,   // Pengadaan Mebel
            29 => 275000000,  // Pengadaan Sarpras Gedung Kantor
            30 => 195000000,  // Pengadaan Peralatan dan Mesin
            31 => 210000000,  // Penyediaan Jasa Komunikasi, Air dan Listrik
            32 => 450000000,  // Pemenuhan Kebutuhan SDM Kesehatan
            33 => 160000000,  // Distribusi dan Pemerataan SDM Kesehatan
            34 => 320000000,  // Peningkatan Kompetensi dan Kualifikasi SDM
            35 => 110000000,  // Pembinaan dan Pengawasan SDM Kesehatan
            36 => 180000000,  // Peningkatan Upaya Advokasi Kesehatan, Pemberdayaan
            37 => 250000000,  // Promosi Kesehatan dan PHBS
            38 => 140000000,  // Penumbuhan Kesadaran Keluarga Derajat Kesehatan
            39 => 95000000,   // Bimtek dan Supervisi UKBM
            40 => 75000000,   // Penyusunan Dokumen Perencanaan Perangkat Daerah
            41 => 60000000,   // Koordinasi dan Penyusunan RKA-SKPD
            42 => 45000000,   // Koordinasi dan Penyusunan Laporan Capaian Kinerja
            43 => 55000000,   // Evaluasi Kinerja Perangkat Daerah
            44 => 40000000,   // Pelaksanaan Pengumpulan Data Statistik Sektoral
            45 => 80000000,   // Pelaksanaan Forum Perangkat Daerah
            46 => 35000000,   // Penyelenggaraan Walidata Statistik Sektoral
            47 => 90000000,   // Pengadaan Pakaian Dinas dan Atribut
            48 => 45000000,   // Pendataan dan Pengolahan Administrasi Kepegawaian
            49 => 70000000,   // SIMPEG
            50 => 55000000,   // Monitoring, Evaluasi dan Penilaian Kinerja Pegawai
            51 => 135000000,  // Pendidikan dan Pelatihan Pegawai (Diklat)
            52 => 50000000,   // Sosialisasi Peraturan Perundang-undangan
        ];

        // Update pagu dan target pada SubKegiatan & Indikator jika kosong
        foreach ($subKegiatans as $sub) {
            $paguBaru = $defaultPagus[$sub->id] ?? ($sub->pagu > 0 ? $sub->pagu : 100000000);
            $targetBaru = $sub->target > 0 ? $sub->target : rand(5, 20);

            $sub->update([
                'pagu' => $paguBaru,
                'target' => $targetBaru,
            ]);

            // Pastikan indikator punya target dan keterangan
            foreach ($sub->indikators as $ind) {
                if (!$ind->target || $ind->target <= 0) {
                    $ind->update([
                        'target' => $targetBaru,
                        'satuan' => $ind->satuan ?: 'Dokumen',
                    ]);
                }
            }
        }

        // Reload data SubKegiatan yang sudah diperbarui
        $subKegiatans = SubKegiatan::with(['kegiatan.program', 'indikators'])->get();

        // 3. Konfigurasi Periode Laporan Dummy (2025 & 2026)
        $periods = [
            // Tahun 2025 (Arsip / Laporan Selesai)
            ['tahun' => 2025, 'bulan' => 'Maret',     'progress_fisik' => 0.28, 'progress_keu' => 0.25],
            ['tahun' => 2025, 'bulan' => 'Juni',      'progress_fisik' => 0.55, 'progress_keu' => 0.52],
            ['tahun' => 2025, 'bulan' => 'September', 'progress_fisik' => 0.82, 'progress_keu' => 0.79],
            ['tahun' => 2025, 'bulan' => 'Desember',  'progress_fisik' => 1.00, 'progress_keu' => 0.98],

            // Tahun 2026 (Tahun Anggaran Berjalan)
            ['tahun' => 2026, 'bulan' => 'Januari',   'progress_fisik' => 0.09, 'progress_keu' => 0.08],
            ['tahun' => 2026, 'bulan' => 'Februari',  'progress_fisik' => 0.17, 'progress_keu' => 0.15],
            ['tahun' => 2026, 'bulan' => 'Maret',     'progress_fisik' => 0.26, 'progress_keu' => 0.24],
            ['tahun' => 2026, 'bulan' => 'April',     'progress_fisik' => 0.35, 'progress_keu' => 0.33],
            ['tahun' => 2026, 'bulan' => 'Mei',       'progress_fisik' => 0.44, 'progress_keu' => 0.41],
            ['tahun' => 2026, 'bulan' => 'Juni',      'progress_fisik' => 0.53, 'progress_keu' => 0.50],
            ['tahun' => 2026, 'bulan' => 'Juli',      'progress_fisik' => 0.62, 'progress_keu' => 0.59],
            ['tahun' => 2026, 'bulan' => 'Agustus',   'progress_fisik' => 0.71, 'progress_keu' => 0.68],
            ['tahun' => 2026, 'bulan' => 'September', 'progress_fisik' => 0.81, 'progress_keu' => 0.77],
            ['tahun' => 2026, 'bulan' => 'Oktober',   'progress_fisik' => 0.89, 'progress_keu' => 0.86],
        ];

        foreach ($periods as $pIndex => $period) {
            $tahun = $period['tahun'];
            $bulan = $period['bulan'];
            $pctFisik = $period['progress_fisik'];
            $pctKeu = $period['progress_keu'];

            $judul = "Laporan Realisasi Keuangan dan Fisik Bulan {$bulan} Tahun Anggaran {$tahun}";

            $laporan = LaporanKonsolidasi::create([
                'judul' => $judul,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'created_at' => now()->subMonths(count($periods) - $pIndex),
                'updated_at' => now()->subMonths(count($periods) - $pIndex),
            ]);

            $kegiatanStats = [];
            $programStats = [];

            // Buat Detail untuk setiap Sub Kegiatan
            foreach ($subKegiatans as $sub) {
                $prog = $sub->kegiatan?->program;
                $keg = $sub->kegiatan;

                if (!$prog || !$keg) {
                    continue;
                }

                $listIndikator = $sub->indikators;
                $textOutput = $listIndikator->pluck('keterangan')->join("\n") ?: ($sub->output ?: 'Terlaksananya kegiatan dan pelaporan');
                $textSatuan = $listIndikator->pluck('satuan')->join("\n") ?: ($sub->satuan ?: 'Dokumen');
                
                $targetTotal = $listIndikator->sum('target');
                if ($targetTotal <= 0) {
                    $targetTotal = $sub->target > 0 ? $sub->target : 10;
                }

                // Variasi sedikit agar data tampak organik dan natural (± 3%)
                $fuzzFisik = (mt_rand(-3, 3) / 100);
                $fuzzKeu = (mt_rand(-4, 2) / 100);

                $currentPctFisik = max(0, min(1.0, $pctFisik + $fuzzFisik));
                $currentPctKeu = max(0, min(1.0, $pctKeu + $fuzzKeu));

                // Hitung realisasi fisik dan realisasi pagu
                $realisasiFisik = round($targetTotal * $currentPctFisik, 1);
                $paguAnggaran = (float) $sub->pagu;
                $paguRealisasi = round($paguAnggaran * $currentPctKeu, 2);

                $namaProgram = $prog->nama ?? $prog->nama_program ?? '-';
                $namaKegiatan = $keg->nama ?? $keg->nama_kegiatan ?? '-';
                $namaLengkap = $namaProgram . ' / ' . $namaKegiatan . ' / ' . $sub->nama;

                // Status verifikasi: 1 (terverifikasi) untuk bulan lama, bulan terbaru sebagian 1 dan sebagian 0
                $isVerified = ($tahun < 2026 || in_array($bulan, ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus'])) ? 1 : (mt_rand(1, 10) > 2 ? 1 : 0);

                $detail = DetailLaporanKonsolidasi::create([
                    'laporan_konsolidasi_id' => $laporan->id,
                    'sub_kegiatan_id'       => $sub->id,
                    'kode'                  => $sub->kode,
                    'nama_program_kegiatan' => $namaLengkap,
                    'sub_output'            => $textOutput,
                    'satuan_unit'           => $textSatuan,
                    'pagu_anggaran'         => $paguAnggaran,
                    'pagu_realisasi'        => $paguRealisasi,
                    'target'                => $targetTotal,
                    'realisasi_fisik'       => $realisasiFisik,
                    'is_verified'           => $isVerified,
                ]);

                // Akumulasi statistik Kegiatan & Program
                $kegId = $keg->id;
                $progId = $prog->id;

                if (!isset($kegiatanStats[$kegId])) {
                    $kegiatanStats[$kegId] = [
                        'pagu' => 0,
                        'realisasi' => 0,
                        'total_persen_fisik' => 0,
                        'count_sub' => 0,
                    ];
                }

                if (!isset($programStats[$progId])) {
                    $programStats[$progId] = [
                        'pagu' => 0,
                        'realisasi' => 0,
                        'total_persen_fisik' => 0,
                        'count_sub' => 0,
                    ];
                }

                $persenFisikSub = ($targetTotal > 0) ? min(100, ($realisasiFisik / $targetTotal) * 100) : 0;

                $kegiatanStats[$kegId]['pagu'] += $paguAnggaran;
                $kegiatanStats[$kegId]['realisasi'] += $paguRealisasi;
                $kegiatanStats[$kegId]['total_persen_fisik'] += $persenFisikSub;
                $kegiatanStats[$kegId]['count_sub']++;

                $programStats[$progId]['pagu'] += $paguAnggaran;
                $programStats[$progId]['realisasi'] += $paguRealisasi;
                $programStats[$progId]['total_persen_fisik'] += $persenFisikSub;
                $programStats[$progId]['count_sub']++;
            }

            // Simpan agregasi ke LaporanKonsolidasiAnggaran untuk KEGIATAN
            foreach ($kegiatanStats as $kegId => $stats) {
                $avgFisik = $stats['count_sub'] > 0 ? ($stats['total_persen_fisik'] / $stats['count_sub']) : 0;

                LaporanKonsolidasiAnggaran::create([
                    'laporan_konsolidasi_id' => $laporan->id,
                    'program_id'             => null,
                    'kegiatan_id'            => $kegId,
                    'pagu_anggaran'          => $stats['pagu'],
                    'pagu_realisasi'         => $stats['realisasi'],
                    'target'                 => 100,
                    'realisasi_fisik'        => round($avgFisik, 2),
                    'is_verified'            => 1,
                ]);
            }

            // Simpan agregasi ke LaporanKonsolidasiAnggaran untuk PROGRAM
            foreach ($programStats as $progId => $stats) {
                $avgFisik = $stats['count_sub'] > 0 ? ($stats['total_persen_fisik'] / $stats['count_sub']) : 0;

                LaporanKonsolidasiAnggaran::create([
                    'laporan_konsolidasi_id' => $laporan->id,
                    'program_id'             => $progId,
                    'kegiatan_id'            => null,
                    'pagu_anggaran'          => $stats['pagu'],
                    'pagu_realisasi'         => $stats['realisasi'],
                    'target'                 => 100,
                    'realisasi_fisik'        => round($avgFisik, 2),
                    'is_verified'            => 1,
                ]);
            }
        }

        $this->command?->info("Dummy Data Laporan E-Monev berhasil dibuat (" . count($periods) . " periode laporan)!");
    }
}
