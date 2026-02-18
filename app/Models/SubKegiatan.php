<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
    'kegiatan_id',
    'output_kegiatan_id',
    'jabatan_id',
    'kode',
    'nama',
    'tahun', 
    'pagu',
    'target',
    'kinerja',
    'satuan'
];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function indikators()
    {
        return $this->hasMany(IndikatorSubKegiatan::class, 'sub_kegiatan_id');
    }

    public function pohonKinerja()
    {
        return $this->hasOne(PohonKinerja::class, 'sub_kegiatan_id');
    }
}