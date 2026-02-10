<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id', 
        'outcome_id', 
        'kode',       
        'nama',       // <--- Tetap 'nama'
        'jabatan_id',
        'pagu',       // <--- Tambahan Baru
        'target'      // <--- Tambahan Baru
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function subKegiatans()
    {
        return $this->hasMany(SubKegiatan::class, 'kegiatan_id');
    }

    public function indikators()
    {
        return $this->hasMany(IndikatorKegiatan::class);
    }

    public function pohonKinerja()
    {
        return $this->hasOne(PohonKinerja::class, 'kegiatan_id');
    }
    
    public function outputs()
    {
        return $this->hasMany(OutputKegiatan::class);
    }
}