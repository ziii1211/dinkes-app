<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class IndikatorSasaran extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $table = 'indikator_sasarans';

    protected $fillable = [
        'sasaran_id',
        'keterangan',
        'satuan',
        'arah',
        'target_2025',
        'target_2026',
        'target_2027',
        'target_2028',
        'target_2029',
        'target_2030',
    ];

    public function sasaran()
    {
        return $this->belongsTo(Sasaran::class);
    }

}