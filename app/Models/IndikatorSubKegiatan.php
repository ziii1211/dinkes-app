<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorSubKegiatan extends Model
{
    use HasFactory;

    // Pastikan fillable ini ada untuk mencegah error saat 'Simpan Indikator'
    protected $fillable = [
        'sub_kegiatan_id',
        'keterangan',
        'satuan',
        // Target
        'target_2025', 'pagu_2025',
        'target_2026', 'pagu_2026',
        'target_2027', 'pagu_2027',
        'target_2028', 'pagu_2028',
        'target_2029', 'pagu_2029',
        'target_2030', 'pagu_2030',
    ];

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }
}