<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PohonKinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'tujuan_id',
        'nama_pohon',
        'parent_id'
    ];

    // Relasi ke Sasaran RPJMD (Tabel Tujuans)
    public function tujuan()
    {
        return $this->belongsTo(Tujuan::class, 'tujuan_id');
    }

    // Relasi ke Parent (Induk)
    public function parent()
    {
        return $this->belongsTo(PohonKinerja::class, 'parent_id');
    }

    // Relasi ke Children (Anak)
    public function children()
    {
        return $this->hasMany(PohonKinerja::class, 'parent_id');
    }

    public function indikators()
    {
        return $this->hasMany(IndikatorPohonKinerja::class, 'pohon_kinerja_id');
    }

    // TAMBAHAN: Relasi untuk mengambil data Crosscutting dimana pohon ini sebagai Sumbernya
    public function crosscuttings()
    {
        return $this->hasMany(CrosscuttingKinerja::class, 'pohon_sumber_id');
    }
}