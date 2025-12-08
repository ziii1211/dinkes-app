<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorPohonKinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'pohon_kinerja_id',
        'nama_indikator',
        'target', // Tambahkan ini agar bisa simpan Nilai
        'satuan', // Tambahkan ini agar bisa simpan Satuan
    ];

    public function pohonKinerja()
    {
        return $this->belongsTo(PohonKinerja::class, 'pohon_kinerja_id');
    }
}