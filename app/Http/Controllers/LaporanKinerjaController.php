<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

// Panggil Model
use App\Models\Sasaran;
use App\Models\Pegawai;

class LaporanKinerjaController extends Controller
{
    public function printBulanan(Request $request)
    {
        // 1. Ambil Filter (Default bulan/tahun sekarang)
        $bulan = $request->input('bulan', date('m')); 
        $tahun = $request->input('tahun', date('Y'));
        
        // Nama Bulan untuk Judul
        $namaBulan = Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F');

        // 2. QUERY DATA (Sasaran -> Indikator -> Realisasi Bulan Ini)
        $dataKinerja = Sasaran::query()
            ->with(['indikators' => function($query) use ($bulan, $tahun) {
                // Ambil realisasi HANYA untuk bulan & tahun yang dipilih
                $query->with(['realisasis' => function($q) use ($bulan, $tahun) {
                    $q->where('bulan', $bulan)
                      ->where('tahun', $tahun);
                }]);
            }])
            ->get();

        // 3. DATA PEJABAT (Untuk Tanda Tangan)
        $user = Auth::user();
        // Cek apakah user terhubung dengan data pegawai
        $yangMelapor = $user->pegawai ?? Pegawai::first(); 
        // Cari atasan (jika ada kolom atasan_id, sesuaikan. Ini contoh ambil pegawai pertama sbg dummy jika null)
        $pejabatPenilai = Pegawai::first(); 
        if($yangMelapor && $yangMelapor->atasan_id) {
            $pejabatPenilai = Pegawai::find($yangMelapor->atasan_id);
        }

        // 4. DATA NARASI (Upaya, Hambatan, RTL) - Dummy/Kosongkan jika belum ada tabelnya
        $penjelasan = [
            'upaya' => ['Melakukan koordinasi data pokok...'], 
            'hambatan' => ['Masih terdapat kendala teknis...'],
            'rtl' => ['Melakukan rapat evaluasi...']
        ];

        // 5. LOAD VIEW EXCEL
        // Pastikan nama file view sesuai dengan Tahap 3
        return view('cetak.laporan-kinerja-excel', [
            'dataKinerja'    => $dataKinerja,
            'bulan'          => strtoupper($namaBulan),
            'tahun'          => $tahun,
            'nama_skpd'      => 'DINAS KESEHATAN PROVINSI KALIMANTAN SELATAN',
            'nama_jabatan'   => $yangMelapor->jabatan->nama_jabatan ?? 'NAMA JABATAN',
            'pejabatPenilai' => $pejabatPenilai,
            'yangMelapor'    => $yangMelapor,
            'penjelasan'     => $penjelasan
        ]);
    }
}