<?php

namespace App\Models;

// Pastikan import ini ada
use Illuminate\Contracts\Auth\MustVerifyEmail; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification; // <--- PENTING: Import notifikasi baru

class User extends Authenticatable implements MustVerifyEmail 
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',        // Sudah ada dari update sebelumnya
        'address',      // Sudah ada dari update sebelumnya
        'password',
        'role',
        'is_banned',
        'vendor_payment_info',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- TAMBAHAN BARU ---
    // Override method bawaan Laravel untuk mengirim reset password
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}