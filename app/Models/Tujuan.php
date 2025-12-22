<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tujuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sasaran_rpjmd',
        'tujuan',
        'jabatan_id', // ID Penanggung Jawab
    ];

    // Relasi: Penanggung Jawab (Jabatan)
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    // Relasi: Indikator Tujuan (Anak 1)
    public function indikators()
    {
        return $this->hasMany(IndikatorTujuan::class);
    }

    // Relasi: Sasaran Renstra (Anak 2 - yang baru ditambahkan)
    public function sasarans()
    {
        return $this->hasMany(Sasaran::class);
    }

    public function pohonKinerja()
    {
        return $this->hasOne(PohonKinerja::class, 'tujuan_id');
    }
}