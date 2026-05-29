<?php

namespace App\Http\Controllers;

use App\Models\LaporanKonsolidasi;
use App\Models\LaporanKonsolidasiAnggaran;
use App\Models\DetailLaporanKonsolidasi;
use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKonsolidasiCetakController extends Controller
{
    public function cetak(Request $request, $id)
    {
        // 1. Ambil Data Laporan Utama
        $laporan = LaporanKonsolidasi::findOrFail($id);

        // 2. Logic Filter Jabatan
        $jabatanId = $request->query('jabatan_id');
        $selectedJabatan = null;
        $isKepalaDinas = false;

        if ($jabatanId) {
            $selectedJabatan = Jabatan::find($jabatanId);
            
            // Cek apakah ini Kepala Dinas (parent_id nya kosong)
            if ($selectedJabatan && is_null($selectedJabatan->parent_id)) {
                $isKepalaDinas = true;
            }
        }

        // 3. AMBIL DATA (DENGAN FILTER JABATAN JIKA BUKAN KADIS)
        $detailsQuery = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan', 'subKegiatan.indikators'])
            ->where('laporan_konsolidasi_id', $id);

        // JIKA BUKAN KEPALA DINAS DAN ADA JABATAN YANG DIPILIH, filter datanya
        if ($selectedJabatan && !$isKepalaDinas) {
            $detailsQuery->whereHas('subKegiatan', function ($q) use ($jabatanId) {
                $q->where('jabatan_id', $jabatanId);
            });
        }

        $detailsRaw = $detailsQuery->get();

        // Cari ID Kegiatan dan Program yang valid
        $validKegiatanIds = $detailsRaw->pluck('subKegiatan.kegiatan_id')->unique()->filter();
        $validProgramIds = Kegiatan::whereIn('id', $validKegiatanIds)->pluck('program_id')->unique()->filter();

        // Urutkan Program
        $programsInReport = Program::whereIn('id', $validProgramIds)
            ->orderByRaw("CASE WHEN kode LIKE 'X%' OR kode LIKE 'x%' THEN 0 ELSE 1 END ASC")
            ->orderBy('kode', 'asc')
            ->get();

        $anggaranRaw = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)->get();

        // 4. Grouping Data (Bentuk Hirarki)
        $groupedData = [];

        foreach ($programsInReport as $prog) {
            $progAnggaran = $anggaranRaw->where('program_id', $prog->id)->first();

            $kegiatans = Kegiatan::whereIn('id', $validKegiatanIds)
                ->where('program_id', $prog->id)
                ->orderBy('kode', 'asc')
                ->get();

            $kegiatansGrouped = [];

            foreach ($kegiatans as $keg) {
                $kegAnggaran = $anggaranRaw->where('kegiatan_id', $keg->id)->first();

                // Ambil Sub Kegiatan dan sekalian URUTKAN berdasarkan Kode
                $subs = $detailsRaw->filter(function ($item) use ($keg) {
                    return $item->subKegiatan?->kegiatan_id == $keg->id;
                })->sortBy(function($item) {
                    return $item->subKegiatan->kode ?? '';
                });

                if ($subs->count() > 0) {
                    $kegiatansGrouped[$keg->id] = [
                        'kegiatan' => $keg,
                        'anggaran' => $kegAnggaran,
                        'details' => $subs
                    ];
                }
            }

            if (count($kegiatansGrouped) > 0) {
                $groupedData[$prog->id] = [
                    'program' => $prog,
                    'anggaran' => $progAnggaran,
                    'kegiatans' => $kegiatansGrouped
                ];
            }
        }

        // 5. Generate PDF
        $pdf = Pdf::loadView('cetak.laporan-konsolidasi-pdf', [
            'laporan' => $laporan,
            'data' => $groupedData,
            'selectedJabatan' => $selectedJabatan
        ])->setPaper('a4', 'landscape');

        // 6. Nama File
        $judulFile = $selectedJabatan ? 'Laporan_E-Monev_' . str_replace(' ', '_', $selectedJabatan->nama) : 'Laporan_Konsolidasi';
        $fileName = $judulFile . '_' . $laporan->bulan . '_' . $laporan->tahun . '.pdf';

        return $pdf->stream($fileName);
    }

    // FUNGSI BARU: CETAK TOP PERFORMER
    public function cetakTopPerformer(Request $request)
    {
        $tahun = $request->query('tahun');
        $jabatanId = $request->query('jabatan_id');

        // Cari laporan konsolidasi terakhir di tahun tersebut untuk base data
        $laporan = LaporanKonsolidasi::where('tahun', $tahun)->orderBy('id', 'desc')->first();

        if (!$laporan) {
            return abort(404, "Data E-Monev untuk tahun {$tahun} belum tersedia di sistem. Silakan isi realisasi terlebih dahulu.");
        }

        $id = $laporan->id;

        // Logic Filter Jabatan
        $selectedJabatan = null;
        $isKepalaDinas = false;

        if ($jabatanId) {
            $selectedJabatan = Jabatan::find($jabatanId);
            if ($selectedJabatan && is_null($selectedJabatan->parent_id)) {
                $isKepalaDinas = true;
            }
        }

        // Ambil data
        $detailsQuery = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan'])
            ->where('laporan_konsolidasi_id', $id);

        if ($selectedJabatan && !$isKepalaDinas) {
            $detailsQuery->whereHas('subKegiatan', function ($q) use ($jabatanId) {
                $q->where('jabatan_id', $jabatanId);
            });
        }

        $detailsRaw = $detailsQuery->get();
        $validKegiatanIds = $detailsRaw->pluck('subKegiatan.kegiatan_id')->unique()->filter();
        $anggaranRaw = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)->get();
        $kegiatans = Kegiatan::whereIn('id', $validKegiatanIds)->get();

        $highestScore = 0;
        $topPerformer = null;

        // Proses Seleksi Top Performer
        foreach ($kegiatans as $keg) {
            $kegAnggaran = $anggaranRaw->where('kegiatan_id', $keg->id)->first();
            
            if ($kegAnggaran) {
                $paguKeg = $kegAnggaran->pagu_anggaran ?? 0;
                $realisasiKeg = $kegAnggaran->pagu_realisasi ?? 0;

                // Hitung Persentase
                $persenKeu = ($paguKeg > 0) ? ($realisasiKeg / $paguKeg * 100) : 0;
                $persenFisik = $kegAnggaran->realisasi_fisik ?? 0;

                // Rata-rata Skor Capaian
                $score = ($persenKeu + $persenFisik) / 2;

                if ($score > $highestScore && $score > 0) {
                    $highestScore = $score;
                    $namaKegiatan = strtoupper($keg->nama ?? $keg->nama_kegiatan);
                    
                    // Kalimat Alasan Otomatis
                    $alasan = "Berdasarkan hasil evaluasi sistem perencanaan dan pelaporan secara objektif, kegiatan <b>\"{$namaKegiatan}\"</b> "
                            . "berhasil meraih predikat sebagai <b>Top Performer</b>.<br><br> Pencapaian ini didasarkan pada perolehan performa kumulatif tertinggi "
                            . "dengan rincian metrik:<br>"
                            . "1. <b>Realisasi Fisik mencapai " . number_format($persenFisik, 2, ',', '.') . "%</b><br>"
                            . "2. <b>Efisiensi Serapan Anggaran (Keuangan) sebesar " . number_format($persenKeu, 2, ',', '.') . "%</b><br><br>"
                            . "Kinerja luar biasa ini menunjukkan optimalisasi penggunaan anggaran serta komitmen pada capaian target kinerja. "
                            . "Pencapaian ini diharapkan dapat menjadi tolak ukur dan motivasi bagi unit kerja lainnya di lingkungan Dinas Kesehatan.";

                    $topPerformer = [
                        'nama_kegiatan' => $namaKegiatan,
                        'persen_fisik' => number_format($persenFisik, 2, ',', '.'),
                        'persen_keu' => number_format($persenKeu, 2, ',', '.'),
                        'score' => number_format($score, 2, ',', '.'),
                        'alasan' => $alasan
                    ];
                }
            }
        }

        if (!$topPerformer) {
            return abort(404, "Belum ada capaian kinerja yang mencukupi untuk dicetak sebagai Top Performer pada parameter ini.");
        }

        // Generate PDF Khusus
        $pdf = Pdf::loadView('cetak.top-performer-pdf', [
            'tahun' => $tahun,
            'selectedJabatan' => $selectedJabatan,
            'topPerformer' => $topPerformer
        ])->setPaper('a4', 'portrait');

        $judulFile = $selectedJabatan ? 'Sertifikat_Top_Performer_' . str_replace(' ', '_', $selectedJabatan->nama) : 'Laporan_Top_Performer';
        return $pdf->stream($judulFile . '_' . $tahun . '.pdf');
    }
}