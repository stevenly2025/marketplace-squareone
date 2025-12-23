<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class BuyerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Hitung Statistik
        $stats = [
            'total_orders' => Order::where('buyer_id', $user->id)->count(),
            // Hitung pengeluaran cuma dari order yang Selesai (Completed) biar akurat
            'total_spent'  => Order::where('buyer_id', $user->id)
                                   ->where('status', 'completed')
                                   ->sum('total_amount'),
            'pending'      => Order::where('buyer_id', $user->id)
                                   ->whereIn('status', ['pending', 'processing', 'shipped'])
                                   ->count(),
        ];

        // 2. Ambil 5 Order Terakhir (Apapun statusnya)
        $recent_orders = Order::where('buyer_id', $user->id)
            ->with(['items.product', 'seller']) // Eager load biar ringan
            ->latest()
            ->take(5)
            ->get();

        return view('buyer.dashboard', compact('stats', 'recent_orders'));
    }
}