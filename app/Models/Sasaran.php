<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sasaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'tujuan_id', // ID Induk (Tujuan Renstra)
        'sasaran',   // Nama Sasaran
        'jabatan_id' // Penanggung Jawab
    ];

    // Relasi ke Induk (Tujuan)
    public function tujuan()
    {
        return $this->belongsTo(Tujuan::class);
    }

    // Relasi ke Penanggung Jawab (Jabatan)
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    // Relasi ke Anak (Indikator Sasaran)
    public function indikators()
    {
        return $this->hasMany(IndikatorSasaran::class);
    }

    // Relasi ke Anak (Outcome) - BARU DITAMBAHKAN
    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }

    public function pohonKinerja()
    {
        return $this->hasOne(PohonKinerja::class, 'sasaran_id');
    }
}