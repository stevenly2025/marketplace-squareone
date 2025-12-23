<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSquareOneProfile
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (auth()->check() && ($user->role === 'buyer')) {
            if (empty($user->phone) || empty($user->address)) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Lengkapi No. HP dan Alamat di profil SquareOne Anda sebelum melanjutkan transaksi.');
            }
        }

        return $next($request);
    }
}