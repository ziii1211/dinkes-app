<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CetakKeputusanKadisController extends Controller
{
    public function cetak(Request $request)
    {
        $tahun = $request->tahun;
        // PERBAIKAN: Tambahkan (int) agar data dari dropdown yang berupa teks berubah jadi angka mutlak
        $bulan = (int) $request->bulan; 

        // Ubah angka bulan jadi nama bulan (misal: 1 -> Januari)
        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        // Tarik SEMUA data tanpa filter jabatan
        $kinerja = DB::table('pk_indikators')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->join('jabatans', 'perjanjian_kinerjas.jabatan_id', '=', 'jabatans.id')
            ->leftJoin('realisasi_kinerjas', function($join) use ($tahun, $bulan) {
                $join->on('realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
                     ->where('realisasi_kinerjas.tahun', '=', $tahun)
                     ->where('realisasi_kinerjas.bulan', '=', $bulan); 
            })
            ->where('perjanjian_kinerjas.tahun', $tahun)
            ->select(
                'jabatans.nama as nama_divisi',
                'pk_sasarans.sasaran as kinerja_utama',
                'pk_indikators.nama_indikator',
                'pk_indikators.satuan',
                DB::raw("CASE 
                    WHEN perjanjian_kinerjas.tahun = 2025 THEN pk_indikators.target_2025
                    WHEN perjanjian_kinerjas.tahun = 2026 THEN pk_indikators.target_2026
                    ELSE pk_indikators.target_2025 
                END as target"),
                'realisasi_kinerjas.realisasi',
                'realisasi_kinerjas.tanggapan'
            )
            ->get();

        $laporanKadis = [];

        foreach ($kinerja as $k) {
            $targetVal = (float) str_replace(',', '.', $k->target ?? 0);
            $realisasiVal = (float) str_replace(',', '.', $k->realisasi ?? 0);
            
            $capaian = 0;
            if ($targetVal > 0) {
                $capaian = ($realisasiVal / $targetVal) * 100;
            }

            // FILTER: Hanya masukkan yang Belum Tercapai (< 100%) atau kosong
            if (is_null($k->realisasi) || $k->realisasi === '' || $capaian < 100) {
                
                $k->kendala = !empty($k->tanggapan) ? $k->tanggapan : '- (Tidak ada keterangan dari divisi)';
                
                // LOGIKA CARA A: Pembuatan Keputusan Otomatis dari Sistem
                if (is_null($k->realisasi) || $k->realisasi === '') {
                    $k->keputusan = 'INSTRUKSI: Teguran tertulis kepada Kepala Divisi untuk segera menginput data capaian bulan '.$namaBulan.'.';
                } elseif ($capaian < 50) {
                    $k->keputusan = 'EVALUASI MENDALAM: Kinerja sangat rendah ('.round($capaian, 1).'%). Lakukan audit internal terkait metode pelaksanaan di bulan '.$namaBulan.'.';
                } elseif ($capaian < 80) {
                    $k->keputusan = 'PERCEPATAN PROGRAM: Kinerja kurang optimal ('.round($capaian, 1).'%). Instruksi kepada divisi untuk mengadakan rakor evaluasi hambatan teknis.';
                } else {
                    $k->keputusan = 'OPTIMALISASI AKHIR: Kinerja hampir tercapai ('.round($capaian, 1).'%). Selesaikan sisa target di bulan berikutnya dengan pendekatan strategi alternatif.';
                }

                $laporanKadis[] = $k;
            }
        }

        // Kelompokkan data berdasarkan divisi agar tampilannya rapi
        $laporanGrouped = collect($laporanKadis)->groupBy('nama_divisi');

        return view('cetak.keputusan-kadis', compact('laporanGrouped', 'tahun', 'namaBulan'));
    }
}