<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        // PERBAIKAN 1: Pastikan tulisannya 'output', bukan 'ouput'
        'output_kegiatan_id', 
        'keterangan',  
        'satuan',      
        
        // Target Tahunan
        'target_2025',
        'target_2026',
        'target_2027',
        'target_2028',
        'target_2029',
        'target_2030',
    ];

    // PERBAIKAN 2: Ubah nama fungsi & model tujuan menjadi OutputKegiatan
    public function output()
    {
        // Pastikan model OutputKegiatan sudah di-import atau dipanggil dengan benar
        return $this->belongsTo(OutputKegiatan::class, 'output_kegiatan_id');
    }
}