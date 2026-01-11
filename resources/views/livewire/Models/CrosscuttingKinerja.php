<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrosscuttingKinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'pohon_sumber_id',
        'skpd_tujuan_id',
        'pohon_tujuan_id'
    ];

    public function pohonSumber()
    {
        return $this->belongsTo(PohonKinerja::class, 'pohon_sumber_id');
    }

    public function skpdTujuan()
    {
        return $this->belongsTo(SkpdTujuan::class, 'skpd_tujuan_id');
    }

    public function pohonTujuan()
    {
        return $this->belongsTo(PohonKinerja::class, 'pohon_tujuan_id');
    }
}