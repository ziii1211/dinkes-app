<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity; // <--- 1. IMPORT TRAIT AUDIT LOG

class Sasaran extends Model
{
    use HasFactory;
    use LogsActivity; // <--- 2. AKTIFKAN CCTV (LOGGING) DI SINI

    protected $fillable = [
        'tujuan_id', // ID Induk (Tujuan Renstra)
        'sasaran',   // Nama Sasaran
        'jabatan_id' // Penanggung Jawab
    ];

    // --- RELASI ANTAR TABEL ---

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

    // Relasi ke Anak (Outcome)
    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }

    // Relasi ke Pohon Kinerja (Cascading)
    public function pohonKinerja()
    {
        return $this->hasOne(PohonKinerja::class, 'sasaran_id');
    }
}