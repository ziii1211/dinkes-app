<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jabatan;
use App\Models\PerjanjianKinerja; // Tambahan untuk menarik data pegawai

class CetakPenilaianDivisiController extends Controller
{
    public function cetak(Request $request)
    {
        $tahun = $request->tahun;
        $jabatan_id = $request->jabatan_id;

        // Ambil data nama divisi/jabatan
        $jabatan = Jabatan::findOrFail($jabatan_id);

        // Ambil data pegawai dari Perjanjian Kinerja
        $pk = PerjanjianKinerja::with('pegawai')
            ->where('jabatan_id', $jabatan_id)
            ->where('tahun', $tahun)
            ->first();
        
        // Jika ada nama pegawainya, tampilkan. Jika kosong, beri teks default.
        $nama_pegawai = $pk && $pk->pegawai ? $pk->pegawai->nama : 'Belum Ada Pejabat / Pegawai';

        // Query Menggabungkan PK, Indikator, dan Realisasinya
        $kinerja = DB::table('pk_indikators')
            ->join('pk_sasarans', 'pk_indikators.pk_sasaran_id', '=', 'pk_sasarans.id')
            ->join('perjanjian_kinerjas', 'pk_sasarans.perjanjian_kinerja_id', '=', 'perjanjian_kinerjas.id')
            ->leftJoin('realisasi_kinerjas', function($join) use ($tahun) {
                $join->on('realisasi_kinerjas.indikator_id', '=', 'pk_indikators.id')
                     ->where('realisasi_kinerjas.tahun', '=', $tahun);
            })
            ->where('perjanjian_kinerjas.jabatan_id', $jabatan_id)
            ->where('perjanjian_kinerjas.tahun', $tahun)
            ->select(
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

        foreach ($kinerja as $k) {
            $targetVal = (float) str_replace(',', '.', $k->target ?? 0);
            $realisasiVal = (float) str_replace(',', '.', $k->realisasi ?? 0);
            
            if (is_null($k->realisasi) || $k->realisasi === '') {
                $k->status = 'Belum Tercapai';
                $k->penjelasan = 'Data realisasi belum diinput oleh divisi terkait.';
            } else if ($realisasiVal >= $targetVal && $targetVal > 0) {
                $k->status = 'Tercapai';
                $k->penjelasan = 'Sesuai Target';
            } else {
                $k->status = 'Belum Tercapai';
                $k->penjelasan = !empty($k->tanggapan) ? $k->tanggapan : 'Kendala belum dijabarkan oleh divisi. Perlu evaluasi lanjutan.';
            }
        }

        // Kirim variabel $nama_pegawai ke view
        return view('cetak.penilaian-divisi', compact('kinerja', 'jabatan', 'tahun', 'nama_pegawai'));
    }
}