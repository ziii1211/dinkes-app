<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity; // <--- 1. Import Trait Audit Log

class IndikatorSasaran extends Model
{
    use HasFactory;
    use LogsActivity; // <--- 2. Aktifkan CCTV (Logging) di sini

    // Nama tabel (opsional, tapi baik untuk kejelasan)
    protected $table = 'indikator_sasarans';

    // Mendefinisikan kolom yang boleh diisi (termasuk target per tahun)
    protected $fillable = [
        'sasaran_id',   // ID Induk (Sasaran Renstra)
        'keterangan',   // Nama Indikator
        'satuan',       // Satuan (Persen, Dokumen, dll)
        'arah',         // Arah (Meningkat, Menurun)
        'target_2025',
        'target_2026',
        'target_2027',
        'target_2028',
        'target_2029',
        'target_2030',
    ];

    // Relasi ke Induk (Sasaran)
    public function sasaran()
    {
        return $this->belongsTo(Sasaran::class);
    }
}