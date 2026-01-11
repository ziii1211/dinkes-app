<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisualisasiRenstra extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Ini kuncinya broo, biar kolom content_data otomatis jadi Array
    protected $casts = [
        'content_data' => 'array',
        'is_locked' => 'boolean',
    ];

    // Relasi ke anak-anaknya (Recursive)
    public function children()
    {
        return $this->hasMany(VisualisasiRenstra::class, 'parent_id');
    }
}