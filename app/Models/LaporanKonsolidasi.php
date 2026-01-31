<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKonsolidasi extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(DetailLaporanKonsolidasi::class);
    }
}