<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',      // Contoh: 'order_created', 'payment_verified'
        'title',     // Judul notif
        'message',   // Isi pesan
        'link',      // Link tujuan saat diklik
        'is_read',   // Status sudah dibaca atau belum
    ];

    // Relasi: Notifikasi milik User siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}