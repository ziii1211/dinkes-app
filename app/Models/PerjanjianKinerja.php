<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerjanjianKinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'jabatan_id',
        'pegawai_id',
        'tahun',
        'keterangan',
        'status',
        'tanggal_penetapan'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function sasarans()
    {
        return $this->hasMany(PkSasaran::class, 'perjanjian_kinerja_id');
    }

    // RELASI BARU
    public function anggarans()
    {
        return $this->hasMany(PkAnggaran::class, 'perjanjian_kinerja_id');
    }
}