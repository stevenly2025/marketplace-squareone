<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Menampilkan semua pesanan milik pembeli
    public function index()
    {
        $orders = Order::where('buyer_id', auth()->id())
            ->with(['seller', 'items'])
            ->latest()
            ->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    // Aksi Pembeli Mengonfirmasi Pesanan Diterima
    public function complete(Order $order)
    {
        // Pastikan hanya pembeli yang bersangkutan dan statusnya sudah dikirim (shipped)
        if ($order->buyer_id !== auth()->id() || $order->status !== 'shipped') {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return back()->with('success', 'Terima kasih! Pesanan SquareOne Anda telah selesai.');
    }
}