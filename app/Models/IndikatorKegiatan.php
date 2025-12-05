<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKegiatan extends Model
{
    use HasFactory;

    // Mendaftarkan kolom yang boleh diisi (termasuk target tahunan)
    protected $fillable = [
        'kegiatan_id', // Relasi ke Kegiatan
        'keterangan',  // Nama Indikator
        'satuan',      // Satuan
        
        // Target Tahunan
        'target_2025',
        'target_2026',
        'target_2027',
        'target_2028',
        'target_2029',
        'target_2030',
    ];

    // Relasi ke Induk (Kegiatan)
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }
}