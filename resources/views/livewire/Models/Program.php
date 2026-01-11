<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama'
    ];

    // Relasi: Satu Program memiliki banyak Outcome
    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }

    // Relasi: Satu Program memiliki banyak Kegiatan (BARU DITAMBAHKAN)
    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }
}