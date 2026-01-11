<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaAksi extends Model
{
    use HasFactory;

    protected $table = 'rencana_aksis';

    protected $fillable = [
        'jabatan_id',
        'tahun',
        'nama_aksi',
        'target',
        'satuan',
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
    
    // Relasi ke Realisasi
    public function realisasies()
    {
        return $this->hasMany(RealisasiRencanaAksi::class, 'rencana_aksi_id');
    }
}