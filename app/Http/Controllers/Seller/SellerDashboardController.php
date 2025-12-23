<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class SellerDashboardController extends Controller
{
    public function index()
    {
        $sellerId = Auth::id();

        // 1. Statistik Toko
        $stats = [
            'total_sales' => Order::whereHas('items.product', function($q) use ($sellerId) {
                                $q->where('seller_id', $sellerId);
                             })->where('status', 'completed')->sum('total_amount'),
            
            'active_products' => Product::where('seller_id', $sellerId)->where('is_active', true)->count(),
            
            'pending_orders' => Order::whereHas('items.product', function($q) use ($sellerId) {
                                    $q->where('seller_id', $sellerId);
                                })->where('status', 'pending')->count(),
        ];

        // 2. Pesanan yang perlu tindakan CEPAT (Status: Pending)
        $need_action = Order::whereHas('items.product', function($q) use ($sellerId) {
                            $q->where('seller_id', $sellerId);
                        })
                        ->with(['buyer', 'items'])
                        ->where('status', 'pending')
                        ->latest()
                        ->take(5)
                        ->get();

        return view('seller.dashboard', compact('stats', 'need_action'));
    }
}