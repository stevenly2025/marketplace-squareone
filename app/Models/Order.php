<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'seller_id',
        'total_amount',
        'status',
        'payment_proof',
        'payment_verified_at',
        'shipping_address',
        'shipping_phone',
        'tracking_number',
        'shipped_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'payment_verified_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relasi ke Buyer (User)
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    // Relasi ke Seller (User/Toko)
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Relasi ke Item Pesanan
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}