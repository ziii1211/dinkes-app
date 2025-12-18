<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkAnggaran extends Model
{
    use HasFactory;
    
    // Pastikan 'nama_program_kegiatan' ada disini
    protected $fillable = [
        'perjanjian_kinerja_id', 
        'sub_kegiatan_id', 
        'nama_program_kegiatan', 
        'anggaran'
    ];

    public function subKegiatan()
    {
        return $this->belongsTo(SubKegiatan::class);
    }
}