<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoCompleteOrders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cari pesanan yang statusnya 'shipped' dan sudah lewat 7 hari
        Order::where('status', 'shipped')
            ->where('shipped_at', '<=', now()->subDays(7))
            ->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

        return $next($request);
    }
}