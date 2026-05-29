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

    // FUNGSI BARU: CETAK TOP PERFORMER (SINKRON DENGAN LIVEWIRE & DINAMIS KADIS)
    public function cetakTopPerformer(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        $jabatanId = $request->query('jabatan_id');
        $alasan = $request->query('alasan'); 

        $selectedJabatan = null;
        if ($jabatanId) {
            $selectedJabatan = Jabatan::find($jabatanId);
        }

        if (!$alasan) {
            return response("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
                <h2 style='color:#f39c12;'>⚠️ Belum Ada Pemenang (Top Performer)</h2>
                <p>Saat ini belum ada kegiatan pada jabatan ini yang mencatatkan realisasi di atas 0%.</p>
                <p>Silakan update realisasi capaian kinerja terlebih dahulu.</p>
                <br>
                <a href='javascript:window.close();' style='padding:10px 20px; background:#3498db; color:white; text-decoration:none; border-radius:5px;'>Tutup Halaman</a>
            </div>", 404);
        }

        // 1. AMBIL DATA KEPALA DINAS SECARA DINAMIS DARI STRUKTUR ORGANISASI
        // Mencari jabatan tertinggi yang parent_id nya null (Kepala Dinas) beserta data pegawainya
        $kadisJabatan = Jabatan::whereNull('parent_id')->with('pegawai')->first();
        $namaKadis = $kadisJabatan?->pegawai?->nama ?? 'Nama Kepala Dinas Belum Diisi';
        $nipKadis = $kadisJabatan?->pegawai?->nip ?? '-';
        
        // Cek juga pangkat/golongan jika ada di table pegawai (misal pembina utama muda)
        $pangkatKadis = $kadisJabatan?->pegawai?->pangkat ?? 'Pembina Utama Madya'; 

        // 2. FORMAT TANGGAL KE BAHASA INDONESIA (MEI, JUNI, DLL)
        \Carbon\Carbon::setLocale('id');
        $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');

        // 3. Eksekusi Cetak PDF dengan mempassing data Kadis asli
        $pdf = Pdf::loadView('cetak.top-performer-pdf', [
            'tahun' => $tahun,
            'selectedJabatan' => $selectedJabatan,
            'alasan' => urldecode($alasan),
            'namaKadis' => $namaKadis,
            'nipKadis' => $nipKadis,
            'pangkatKadis' => $pangkatKadis,
            'tanggalCetak' => $tanggalCetak
        ])->setPaper('a4', 'portrait');

        $judulFile = $selectedJabatan ? 'Sertifikat_Top_Performer_' . str_replace(' ', '_', $selectedJabatan->nama) : 'Laporan_Top_Performer';
        
        return $pdf->stream($judulFile . '_' . $tahun . '.pdf');
    }
}