<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
    'kode', 
    'nama', 
    'nama_program', // jaga-jaga kalau ada alias
    'tahun', // <--- TAMBAHKAN INI
    'pagu', 
    'realisasi'
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