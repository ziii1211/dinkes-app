<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'outcome_id',
        'keterangan',
        'satuan',
        'arah', // Tambahkan kolom ini agar bisa disimpan
        'target_2025', 
        'target_2026', 
        'target_2027', 
        'target_2028', 
        'target_2029', 
        'target_2030',
    ];

    public function outcome()
    {
        return $this->belongsTo(Outcome::class);
    }
}