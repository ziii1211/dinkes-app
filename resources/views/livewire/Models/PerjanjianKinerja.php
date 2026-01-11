<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity; // <--- 1. IMPORT TRAIT AUDIT LOG

class PerjanjianKinerja extends Model
{
    use HasFactory;
    use LogsActivity; // <--- 2. AKTIFKAN CCTV (LOGGING) DI SINI

    // Nama tabel di database
    protected $table = 'perjanjian_kinerjas';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'jabatan_id',
        'pegawai_id',
        'tahun',
        'keterangan',
        'status',             // Status umum (Draft/Final)
        'tanggal_penetapan',
        
        // --- KOLOM TAMBAHAN (VERIFIKASI) ---
        'status_verifikasi',  // 'disetujui', 'ditolak', 'menunggu'
        'tanggal_verifikasi', // Tanggal disetujui
        'catatan_pimpinan'    // Catatan revisi dari atasan
    ];

    // --- RELASI ANTAR TABEL ---

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function sasarans()
    {
        // Pastikan foreign key di tabel pk_sasarans bernama 'perjanjian_kinerja_id'
        return $this->hasMany(PkSasaran::class, 'perjanjian_kinerja_id');
    }

    public function anggarans()
    {
        // Pastikan foreign key di tabel pk_anggarans bernama 'perjanjian_kinerja_id'
        return $this->hasMany(PkAnggaran::class, 'perjanjian_kinerja_id');
    }
}