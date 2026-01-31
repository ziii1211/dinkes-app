<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailLaporanKonsolidasi extends Model
{
    protected $guarded = [];

    public function laporan()
    {
        return $this->belongsTo(LaporanKonsolidasi::class);
    }

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id');
    }
}