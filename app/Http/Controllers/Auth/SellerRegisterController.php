<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class SellerRegisterController extends Controller
{
    /**
     * Tampilkan Form Register Khusus Seller
     */
    public function create(): View
    {
        return view('auth.register-seller');
    }

    /**
     * Proses Simpan Data Seller
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'], // Nama Toko / Penjual
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20'], // Tambahan: Nomor HP Penting buat seller
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 🔥 INI KUNCINYA: Force 'role' jadi 'seller'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'seller', // <--- Otomatis jadi Seller
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Langsung arahkan ke Dashboard Seller (lewat logic redirect dashboard kita tadi)
        return redirect(route('dashboard', absolute: false));
    }
}