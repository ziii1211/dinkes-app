<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PkSasaran extends Model
{
    use HasFactory;
    protected $fillable = ['perjanjian_kinerja_id', 'sasaran'];

    public function indikators()
    {
        return $this->hasMany(PkIndikator::class, 'pk_sasaran_id');
    }
}