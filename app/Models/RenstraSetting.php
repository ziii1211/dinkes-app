<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RenstraSetting extends Model
{
    use HasFactory;

    protected $table = 'renstra_settings';
    
    // Izinkan semua kolom diisi
    protected $guarded = ['id'];
}