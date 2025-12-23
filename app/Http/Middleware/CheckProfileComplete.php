<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Cek apakah HP atau Alamat masih kosong
        if (empty($user->phone) || empty($user->address)) {
            // Lempar ke halaman edit profil dengan pesan error
            return redirect()->route('profile.edit')
                ->with('error', '⚠️ Tolong lengkapi No. HP & Alamat Anda untuk melanjutkan pengiriman.');
        }

        return $next($request);
    }
}