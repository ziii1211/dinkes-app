<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailLaporanKonsolidasi extends Model
{
    use HasFactory;

    protected $table = 'detail_laporan_konsolidasis';

    // Kita gunakan fillable agar lebih aman dan memastikan kolom baru terdaftar
    protected $fillable = [
        'laporan_konsolidasi_id',
        'sub_kegiatan_id',
        'kode',
        'nama_program_kegiatan',
        'sub_output',
        'satuan_unit',
        'pagu_anggaran',
        'pagu_realisasi',
        'target',           // <--- PENTING: Tambahkan ini agar tidak hilang saat simpan
        'realisasi_fisik'   // <--- PENTING: Tambahkan ini agar tidak hilang saat simpan
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanKonsolidasi::class, 'laporan_konsolidasi_id');
    }

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }
}