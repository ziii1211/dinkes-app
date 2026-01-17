<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKegiatan extends Model
{
    use HasFactory;

    // PERBAIKAN: Menambahkan properti fillable agar bisa diinput via create()
    protected $fillable = [
        'kegiatan_id',
        'output_kegiatan_id', // <--- TAMBAHKAN INI
        'kode',
        'nama',
        'output',
        'jabatan_id'
    ];

    // Relasi ke Kegiatan (Induk)
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    // Relasi ke Jabatan (Penanggung Jawab)
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    // Relasi ke Indikator (Anak)
    public function indikators()
    {
        return $this->hasMany(IndikatorSubKegiatan::class, 'sub_kegiatan_id');
    }

    public function pohonKinerja()
    {
        return $this->hasOne(PohonKinerja::class, 'sub_kegiatan_id');
    }
}