<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',     // <--- Pastikan ini 'nama', bukan 'nama_program'
        'pagu',     // <--- Tambahan Baru
        'target'    // <--- Tambahan Baru
    ];

    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }
}