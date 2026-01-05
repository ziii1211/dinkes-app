<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjelasanKinerja extends Model
{
    use HasFactory;

    protected $table = 'penjelasan_kinerjas';

    protected $fillable = [
        'jabatan_id',
        'bulan',
        'tahun',
        'upaya',         // Baru
        'hambatan',      // Baru
        'tindak_lanjut', // Baru
    ];

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}