<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Siapkan Data Statistik
        $stats = [
            'total_sales'    => Order::where('status', 'completed')->sum('total_amount'),
            'total_orders'   => Order::count(),
            'total_users'    => User::count(),
            'total_products' => Product::count(),
        ];

        // 2. Siapkan Data Order Terbaru (5 biji aja)
        $recent_orders = Order::with(['buyer', 'seller'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Kirim ke View
        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }
}