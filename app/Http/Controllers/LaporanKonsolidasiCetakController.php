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

        // 2. Logic Jabatan (HANYA UNTUK TANDA TANGAN)
        // Kita ambil data jabatan jika user memilihnya di pop-up
        $jabatanId = $request->query('jabatan_id');
        $selectedJabatan = null;

        if ($jabatanId) {
            $selectedJabatan = Jabatan::find($jabatanId);
        }

        // 3. AMBIL DATA (FULL - TANPA FILTER JABATAN)
        // Logika ini dikembalikan seperti semula agar data tabel persis seperti di input

        $programIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)
            ->whereNotNull('program_id')
            ->pluck('program_id');

        // Urutkan Program
        $programsInReport = Program::whereIn('id', $programIds)
            ->orderByRaw("CASE WHEN kode LIKE 'X%' OR kode LIKE 'x%' THEN 0 ELSE 1 END ASC")
            ->orderBy('kode', 'asc')
            ->get();

        // Ambil Raw Data Detail & Anggaran
        $detailsRaw = DetailLaporanKonsolidasi::with(['subKegiatan.kegiatan', 'subKegiatan.indikators'])
            ->where('laporan_konsolidasi_id', $id)
            ->get();

        $anggaranRaw = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)->get();

        // 4. Grouping Data
        $groupedData = [];

        foreach ($programsInReport as $prog) {
            $progAnggaran = $anggaranRaw->where('program_id', $prog->id)->first();

            $groupedData[$prog->id] = [
                'program' => $prog,
                'anggaran' => $progAnggaran,
                'kegiatans' => []
            ];

            $kegiatanIds = LaporanKonsolidasiAnggaran::where('laporan_konsolidasi_id', $id)
                ->whereNotNull('kegiatan_id')
                ->whereHas('kegiatan', function ($q) use ($prog) {
                    $q->where('program_id', $prog->id);
                })
                ->pluck('kegiatan_id');

            $kegiatans = Kegiatan::whereIn('id', $kegiatanIds)->orderBy('kode', 'asc')->get();

            foreach ($kegiatans as $keg) {
                $kegAnggaran = $anggaranRaw->where('kegiatan_id', $keg->id)->first();

                // Ambil details milik kegiatan ini
                $subs = $detailsRaw->filter(function ($item) use ($keg) {
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
        // Kita kirim $selectedJabatan untuk logika Tanda Tangan di View
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
