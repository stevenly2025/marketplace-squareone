<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke User (Pembeli yang kasih review)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}