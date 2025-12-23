<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_banned) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun SquareOne Anda telah ditangguhkan. Silakan hubungi admin.');
        }

        return $next($request);
    }
}