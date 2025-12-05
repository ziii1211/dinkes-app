<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorTujuan extends Model
{
    use HasFactory;

    // DAFTAR KOLOM YANG BOLEH DISIMPAN
    protected $fillable = [
        'tujuan_id',
        'keterangan',
        'satuan',
        'arah',
        // Kolom Target wajib didaftarkan disini:
        'target_2025',
        'target_2026',
        'target_2027',
        'target_2028',
        'target_2029',
        'target_2030',
    ];

    public function tujuan()
    {
        return $this->belongsTo(Tujuan::class);
    }
}