<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek apakah user login DAN punya role yang sesuai
        if (! $request->user() || $request->user()->role !== $role) {
            // Jika tidak cocok, tendang ke halaman 403 (Unauthorized)
            abort(403, 'Akses ditolak. Anda bukan ' . $role);
        }

        return $next($request);
    }
}