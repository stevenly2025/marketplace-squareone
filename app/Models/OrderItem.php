<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Ganti $fillable dengan ini biar semua kolom boleh diisi
    protected $guarded = ['id']; 

    // Relasi ke Order Utama
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}