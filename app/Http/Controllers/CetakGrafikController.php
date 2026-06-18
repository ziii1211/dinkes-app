<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CetakGrafikController extends Controller
{
    public function cetak(Request $request)
    {
        $tahun = $request->tahun;
        $bulan = (int) $request->bulan;
        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        // Tarik data indikator dan gabungkan dengan realisasi
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
                DB::raw("CASE 
                    WHEN perjanjian_kinerjas.tahun = 2025 THEN pk_indikators.target_2025
                    WHEN perjanjian_kinerjas.tahun = 2026 THEN pk_indikators.target_2026
                    ELSE pk_indikators.target_2025 
                END as target"),
                'realisasi_kinerjas.realisasi'
            )
            ->get();

        // Hitung rata-rata persentase per divisi
        $divisiData = [];
        foreach ($kinerja as $k) {
            $targetVal = (float) str_replace(',', '.', $k->target ?? 0);
            $realisasiVal = (float) str_replace(',', '.', $k->realisasi ?? 0);
            
            $capaian = 0;
            if ($targetVal > 0) {
                $capaian = ($realisasiVal / $targetVal) * 100;
                if($capaian > 100) $capaian = 100; // Cap maksimal 100% untuk grafik
            }

            if(!isset($divisiData[$k->nama_divisi])) {
                $divisiData[$k->nama_divisi] = ['total' => 0, 'count' => 0];
            }
            $divisiData[$k->nama_divisi]['total'] += $capaian;
            $divisiData[$k->nama_divisi]['count']++;
        }

        $categories = [];
        $seriesData = [];
        $totalAll = 0;
        $countAll = 0;
        
        $highestDivisi = '';
        $lowestDivisi = '';
        $maxCapaian = -1;
        $minCapaian = 101;

        foreach($divisiData as $nama => $data) {
            $avg = $data['count'] > 0 ? round($data['total'] / $data['count'], 2) : 0;
            $categories[] = $nama;
            $seriesData[] = $avg;
            
            if ($avg > $maxCapaian) {
                $maxCapaian = $avg;
                $highestDivisi = $nama;
            }
            if ($avg < $minCapaian) {
                $minCapaian = $avg;
                $lowestDivisi = $nama;
            }
            
            $totalAll += $avg;
            $countAll++;
        }

        $rataRataTotal = $countAll > 0 ? round($totalAll / $countAll, 2) : 0;
        $kategoriText = $rataRataTotal >= 80 ? 'Sangat Baik' : ($rataRataTotal >= 60 ? 'Baik' : 'Perlu Perhatian');

        // Bikin Narasi Otomatis (Akan tampil di bawah grafik PDF)
        $penjelasan = "Berdasarkan grafik capaian kinerja pada Bulan {$namaBulan} Tahun {$tahun}, rata-rata capaian kinerja secara keseluruhan adalah sebesar {$rataRataTotal}% (Kategori: {$kategoriText}). ";
        
        if ($countAll > 0 && $rataRataTotal > 0) {
            if ($maxCapaian == $minCapaian) {
                $penjelasan .= "Seluruh divisi/bidang mencatatkan capaian kinerja yang merata di angka {$maxCapaian}%. ";
            } else {
                $penjelasan .= "Capaian tertinggi diraih oleh {$highestDivisi} sebesar {$maxCapaian}%, sementara capaian terendah tercatat pada {$lowestDivisi} sebesar {$minCapaian}%. ";
            }
        }

        if($rataRataTotal < 100 && $rataRataTotal > 0) {
            $penjelasan .= "Berdasarkan hasil tersebut, diperlukan langkah-langkah strategis dan evaluasi kinerja secara berkala, khususnya pada divisi dengan capaian yang masih di bawah target, agar optimalisasi pencapaian dapat direalisasikan pada periode berikutnya.";
        } elseif ($rataRataTotal == 0) {
            $penjelasan .= "Saat ini data realisasi belum tersedia atau masih bernilai 0%. Diperlukan atensi khusus dan instruksi segera kepada seluruh jajaran terkait untuk melakukan pembaruan data kinerja.";
        } else {
            $penjelasan .= "Kinerja telah mencapai tingkat yang sangat optimal dengan seluruh divisi merealisasikan target secara maksimal. Prestasi ini diharapkan dapat terus dipertahankan secara konsisten.";
        }

        $tanggal_cetak = Carbon::now()->translatedFormat('d F Y');

        // Data dikirim ke file view PDF
        return view('cetak.laporan-grafik', [
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'categories' => json_encode($categories), // Ubah jadi JSON agar bisa dibaca JS di View
            'seriesData' => json_encode($seriesData),
            'penjelasan' => $penjelasan,
            'tanggal_cetak' => $tanggal_cetak
        ]);
    }
}