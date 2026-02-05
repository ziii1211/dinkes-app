<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKonsolidasiAnggaran extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke Laporan Konsolidasi
    public function laporanKonsolidasi()
    {
        return $this->belongsTo(LaporanKonsolidasi::class);
    }

    // Relasi ke Program (PENTING: Tambahkan ini)
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Relasi ke Kegiatan (PENTING: Tambahkan ini untuk fix error)
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}