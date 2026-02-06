<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKonsolidasiAnggaran extends Model
{
    use HasFactory;

    protected $table = 'laporan_konsolidasi_anggarans';

    // Menggunakan $fillable agar lebih aman dan memastikan kolom baru bisa diisi
    protected $fillable = [
        'laporan_konsolidasi_id',
        'program_id',
        'kegiatan_id',
        'pagu_anggaran',
        'pagu_realisasi',
        'target',           // <--- PENTING: Kolom baru agar data tidak hilang
        'realisasi_fisik'   // <--- PENTING: Kolom baru agar data tidak hilang
    ];

    // Relasi ke Laporan Konsolidasi
    public function laporanKonsolidasi()
    {
        return $this->belongsTo(LaporanKonsolidasi::class);
    }

    // Relasi ke Program
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Relasi ke Kegiatan
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}