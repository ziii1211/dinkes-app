<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiKinerja extends Model
{
    use HasFactory;

    protected $table = 'realisasi_kinerjas';

    protected $fillable = [
        'indikator_id',
        'bulan',
        'tahun',
        'realisasi',
        'capaian',
        'catatan',
        'tanggapan',
    ];
}