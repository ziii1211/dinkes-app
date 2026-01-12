<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputKegiatan extends Model
{
    use HasFactory;

    protected $fillable = ['kegiatan_id', 'deskripsi', 'jabatan_id'];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function indikators()
    {
        return $this->hasMany(IndikatorKegiatan::class, 'output_kegiatan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
}