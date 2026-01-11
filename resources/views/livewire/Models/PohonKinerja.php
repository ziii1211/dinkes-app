<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PohonKinerja extends Model
{
    protected $guarded = [];

    // --- RELASI KE PARENT (ITEM RENSTRA) ---
    public function tujuan() 
    { 
        return $this->belongsTo(Tujuan::class, 'tujuan_id'); 
    }
    
    public function sasaran() 
    { 
        return $this->belongsTo(Sasaran::class, 'sasaran_id'); 
    }
    
    public function outcome() 
    { 
        return $this->belongsTo(Outcome::class, 'outcome_id'); 
    }
    
    public function kegiatan() 
    { 
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id'); 
    }
    
    public function subKegiatan() 
    { 
        return $this->belongsTo(SubKegiatan::class, 'sub_kegiatan_id'); 
    }

    // --- RELASI INTERNAL POHON ---
    
    // 1. Relasi Indikator
    public function indikators()
    {
        return $this->hasMany(IndikatorPohonKinerja::class, 'pohon_kinerja_id');
    }

    // 2. Relasi Parent (Atasan)
    public function parent()
    {
        return $this->belongsTo(PohonKinerja::class, 'parent_id');
    }

    // 3. Relasi Childs (Bawahan) - Gunakan nama 'childs'
    public function childs()
    {
        return $this->hasMany(PohonKinerja::class, 'parent_id');
    }

    // 4. Relasi Children (Alias untuk Childs) - PENYELAMAT ERROR
    // Ini ditambahkan agar kode lama yang memanggil 'children' tetap jalan
    public function children()
    {
        return $this->childs();
    }
}