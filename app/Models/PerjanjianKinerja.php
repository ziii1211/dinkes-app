<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerjanjianKinerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'jabatan_id',
        'pegawai_id',
        'tahun',
        'keterangan',
        'status',             // Status umum (jika masih dipakai)
        'tanggal_penetapan',
        
        // --- TAMBAHAN PENTING ---
        'status_verifikasi',  // Wajib ada agar bisa update status ke 'disetujui'
        'tanggal_verifikasi', // Wajib ada untuk mencatat tanggal publikasi
        'catatan_pimpinan'    // Tambahkan juga jaga-jaga untuk catatan revisi
    ];

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
        return $this->hasMany(PkSasaran::class, 'perjanjian_kinerja_id');
    }

    public function anggarans()
    {
        return $this->hasMany(PkAnggaran::class, 'perjanjian_kinerja_id');
    }
}