<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'sasaran_id', 
        'program_id', // Tambahkan ini agar bisa disimpan
        'outcome',
        'jabatan_id'
    ];

    // Relasi ke Sasaran (Induk di Matrik Renstra)
    public function sasaran()
    {
        return $this->belongsTo(Sasaran::class);
    }

    // Relasi ke Program (Induk di Program/Kegiatan) - BARU
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    // Relasi ke Penanggung Jawab
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }

    // Relasi ke Indikator Outcome
    public function indikators()
    {
        return $this->hasMany(IndikatorOutcome::class);
    }

    public function kegiatans()
{
    // Asumsi: Outcome terhubung ke Kegiatan lewat Program yang sama, 
    // atau jika ada kolom outcome_id di tabel kegiatan. 
    // Jika via program:
    return $this->hasManyThrough(Kegiatan::class, Program::class, 'id', 'program_id', 'program_id', 'id');
    
    // TAPI, jika struktur DB Anda sederhana (Outcome -> Kegiatan langsung), pakai ini:
    // return $this->hasMany(Kegiatan::class);
    
    // SEMENTARA KITA GUNAKAN LOGIKA MANUAL DI CONTROLLER AGAR AMAN
    // (Silakan lewati edit model ini jika ragu struktur DB-nya)
}

public function pohonKinerja()
    {
        return $this->hasOne(PohonKinerja::class, 'outcome_id');
    }
}