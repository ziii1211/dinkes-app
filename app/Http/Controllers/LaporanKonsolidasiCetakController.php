<?php

namespace App\Http\Controllers;

use App\Models\LaporanKonsolidasi;
use App\Models\LaporanKonsolidasiAnggaran;
use App\Models\DetailLaporanKonsolidasi;
use App\Models\Program;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKonsolidasiCetakController extends Controller
{
    public function cetak($id)
    {
        // 1. Ambil Data Laporan Utama
        $laporan = LaporanKonsolidasi::findOrFail($id);

        // 2. Ambil ID Program yang ada di laporan ini
        $programIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)
                        ->whereNotNull('program_id')
                        ->pluck('program_id');
        
        $programsInReport = Program::whereIn('id', $programIds)->orderBy('kode', 'asc')->get();

        // 3. Ambil Data Detail (Sub Kegiatan) dan Anggaran (Program/Kegiatan)
        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan', 'subKegiatan.indikators'])
                        ->where('laporan_konsolidasi_id', $id)->get();
        
        $anggaranRaw = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)->get();

        // 4. Struktur Data untuk View (Grouping)
        $groupedData = [];
        foreach($programsInReport as $prog) {
            // Ambil data anggaran Program
            $progAnggaran = $anggaranRaw->where('program_id', $prog->id)->first();

            $groupedData[$prog->id] = [
                'program' => $prog,
                'anggaran' => $progAnggaran,
                'kegiatans' => []
            ];
            
            // Ambil ID Kegiatan
            $kegiatanIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)
                            ->whereNotNull('kegiatan_id')
                            ->whereHas('kegiatan', function($q) use ($prog) { 
                                $q->where('program_id', $prog->id); 
                            })
                            ->pluck('kegiatan_id');
            
            $kegiatans = Kegiatan::whereIn('id', $kegiatanIds)->orderBy('kode', 'asc')->get();

            foreach($kegiatans as $keg) {
                // Ambil data anggaran Kegiatan
                $kegAnggaran = $anggaranRaw->where('kegiatan_id', $keg->id)->first();

                // Filter sub kegiatan
                $subs = $detailsRaw->filter(function($item) use ($keg) {
                    return $item->subKegiatan?->kegiatan_id == $keg->id;
                });

                $groupedData[$prog->id]['kegiatans'][$keg->id] = [
                    'kegiatan' => $keg,
                    'anggaran' => $kegAnggaran,
                    'details' => $subs
                ];
            }
        }

        // 5. Generate PDF
        $pdf = Pdf::loadView('cetak.laporan-konsolidasi-pdf', [
            'laporan' => $laporan,
            'data' => $groupedData
        ])->setPaper('a4', 'landscape'); // Format Landscape agar muat banyak kolom

        // 6. Nama File Download
        $fileName = 'Laporan_Konsolidasi_' . $laporan->bulan . '_' . $laporan->tahun . '.pdf';
        return $pdf->stream($fileName);
    }
}