<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi Input (Tambah Image)
        $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:500',
            'image'      => 'nullable|image|max:2048', // <--- BARU: Validasi Gambar (Max 2MB)
        ]);

        // 2. Keamanan User
        $order = Order::find($request->order_id);
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'Anda tidak berhak mereview pesanan ini.');
        }

        // 3. Cek Spam Review
        $existingReview = Review::where('order_id', $request->order_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        // 4. LOGIKA UPLOAD GAMBAR (INI YANG KEMARIN HILANG)
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Simpan ke folder 'storage/app/public/reviews'
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        // 5. Simpan ke Database
        Review::create([
            'user_id'    => auth()->id(),
            'order_id'   => $request->order_id,
            'product_id' => $request->product_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
            'image'      => $imagePath, // <--- BARU: Masukkan path gambar ke DB
        ]);

        return back()->with('success', 'Terima kasih! Ulasan dan foto berhasil dikirim.');
    }
}