<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    // INI YANG KURANG BRO! 👇
    // Kita kasih izin kolom-kolom ini buat diisi data
    protected $fillable = [
        'image',
        'link',
        'is_active',
    ];
}