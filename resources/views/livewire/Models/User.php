<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',     // Pastikan role masuk fillable
        'nip',      // Pastikan nip masuk fillable
        'jabatan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Helper cek role
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * RELASI PENTING: Menghubungkan User ke Pegawai via NIP
     * Syarat: NIP di tabel users harus SAMA PERSIS dengan NIP di tabel pegawais
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }
}