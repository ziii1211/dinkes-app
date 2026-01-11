<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiRencanaAksi extends Model
{
    use HasFactory;

    protected $table = 'realisasi_rencana_aksis';

    protected $fillable = [
        'rencana_aksi_id',
        'bulan',
        'tahun',
        'realisasi',
    ];

    public function rencanaAksi()
    {
        return $this->belongsTo(RencanaAksi::class, 'rencana_aksi_id');
    }
}