<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkIndikator extends Model
{
    use HasFactory;
    protected $fillable = [
        'pk_sasaran_id', 
        'nama_indikator', 
        'satuan', 
        'arah',
        // Target Per Tahun
        'target_2025', 'target_2026', 'target_2027', 
        'target_2028', 'target_2029', 'target_2030'
    ];
}