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

        if ($jabatanId) {
            $selectedJabatan = Jabatan::find($jabatanId);
        }

        // 3. AMBIL DATA (DENGAN FILTER JABATAN JIKA ADA)
        
        // Mulai query untuk mengambil Detail (Sub Kegiatan)
        $detailsQuery = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan', 'subKegiatan.indikators'])
            ->where('laporan_konsolidasi_id', $id);

        // JIKA ADA FILTER JABATAN, maka ambil HANYA Sub Kegiatan yang jabatan_id-nya cocok
        if ($jabatanId) {
            $detailsQuery->whereHas('subKegiatan', function ($q) use ($jabatanId) {
                $q->where('jabatan_id', $jabatanId);
            });
        }

        $detailsRaw = $detailsQuery->get();

        // Cari ID Kegiatan dan Program yang valid (yang memiliki Sub Kegiatan terkait PJ)
        $validKegiatanIds = $detailsRaw->pluck('subKegiatan.kegiatan_id')->unique()->filter();
        $validProgramIds = Kegiatan::whereIn('id', $validKegiatanIds)->pluck('program_id')->unique()->filter();

        // Urutkan Program yang hanya masuk dalam filter
        $programsInReport = Program::whereIn('id', $validProgramIds)
            ->orderByRaw("CASE WHEN kode LIKE 'X%' OR kode LIKE 'x%' THEN 0 ELSE 1 END ASC")
            ->orderBy('kode', 'asc')
            ->get();

        $anggaranRaw = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)->get();

        // 4. Grouping Data (Bentuk Hirarki)
        $groupedData = [];

        foreach ($programsInReport as $prog) {
            $progAnggaran = $anggaranRaw->where('program_id', $prog->id)->first();

            // Cari Kegiatan untuk Program ini yang ada di dalam filter
            $kegiatans = Kegiatan::whereIn('id', $validKegiatanIds)
                ->where('program_id', $prog->id)
                ->orderBy('kode', 'asc')
                ->get();

            $kegiatansGrouped = [];

            foreach ($kegiatans as $keg) {
                $kegAnggaran = $anggaranRaw->where('kegiatan_id', $keg->id)->first();

                // Ambil Sub Kegiatan yang cuma milik kegiatan ini
                $subs = $detailsRaw->filter(function ($item) use ($keg) {
                    return $item->subKegiatan?->kegiatan_id == $keg->id;
                });

                // Pastikan kegiatan ini punya isi sebelum dimasukkan
                if ($subs->count() > 0) {
                    $kegiatansGrouped[$keg->id] = [
                        'kegiatan' => $keg,
                        'anggaran' => $kegAnggaran,
                        'details' => $subs
                    ];
                }
            }

            // Pastikan program ini punya kegiatan sebelum dimasukkan
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
        $judulFile = $selectedJabatan ? 'Laporan_' . str_replace(' ', '_', $selectedJabatan->nama) : 'Laporan_Konsolidasi';
        $fileName = $judulFile . '_' . $laporan->bulan . '_' . $laporan->tahun . '.pdf';

        return $pdf->stream($fileName);
    }
}