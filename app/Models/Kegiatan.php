<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'program_id', 
        'outcome_id', // <--- INI BARU// Relasi ke Program
        'kode',       // Kode Kegiatan
        'nama',       // Nama Kegiatan
        // 'output',     // Deskripsi Output
        'jabatan_id'  // Tambahkan ini (Penanggung Jawab)
    ];

    // Relasi ke Induk (Program)
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // Relasi ke Anak (Indikator Kegiatan)
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