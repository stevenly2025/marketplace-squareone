<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 1. Relasi ke Reviews (Wajib ada untuk Rating & Ulasan)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // 2. Relasi ke OrderItems (Wajib ada untuk hitung JUMLAH BARANG terjual)
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // 3. Relasi ke Orders (Opsional/Legacy: Jaga-jaga biar Controller lain gak error)
    public function orders()
    {
        // Menghubungkan Produk -> OrderItem -> Order
        return $this->hasManyThrough(Order::class, OrderItem::class, 'product_id', 'id', 'id', 'order_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}