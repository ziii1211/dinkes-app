<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke Pegawai
    // Asumsi: Tabel 'pegawais' memiliki kolom 'jabatan_id'
    // Artinya: Jabatan ini dimiliki oleh satu Pegawai (Pejabatnya)
    public function pegawai()
    {
        return $this->hasOne(Pegawai::class, 'jabatan_id');
    }

    // Jika tabel jabatan punya parent (untuk struktur pohon)
    public function parent()
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Jabatan::class, 'parent_id');
    }
}