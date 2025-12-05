<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrosscuttingRenstra extends Model
{
    use HasFactory;

    protected $fillable = [
        'sumber_type',
        'sumber_id',
        'skpd_tujuan_id',
        'tujuan_type',
        'tujuan_id'
    ];

    // Relasi Polymorphic ke Sumber
    public function sumber()
    {
        return $this->morphTo();
    }

    // Relasi ke SKPD
    public function skpd()
    {
        return $this->belongsTo(SkpdTujuan::class, 'skpd_tujuan_id');
    }

    // Relasi Polymorphic ke Tujuan
    public function tujuan()
    {
        return $this->morphTo();
    }
}