<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    // PINTU 1: DARI KERANJANG (Selected Items)
    public function index(Request $request)
    {
        // Validasi: User harus pilih minimal 1 barang
        if (!$request->has('cart_ids')) {
            return redirect()->route('buyer.cart.index')->with('error', 'Pilih minimal satu barang untuk checkout!');
        }

        $cartIds = $request->cart_ids;
        
        // Ambil data keranjang berdasarkan ID yang DIPILIH saja
        $cartItems = Cart::whereIn('id', $cartIds)
                        ->where('user_id', Auth::id())
                        ->with('product.seller')
                        ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index');
        }

        // Hitung Total
        $totalWeight = 0;
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $totalWeight += $item->product->weight * $item->quantity; // Asumsi ada kolom weight
            $subtotal += $item->product->price * $item->quantity;
        }

        // Kirim data ke View dengan flag 'source' = 'cart'
        return view('buyer.checkout', [
            'items' => $cartItems,
            'totalWeight' => $totalWeight,
            'subtotal' => $subtotal,
            'source' => 'cart', // Penanda ini dari keranjang
            'cart_ids' => $cartIds // Kita butuh ini buat hapus nanti pas sukses bayar
        ]);
    }

    // PINTU 2: BELI SEKARANG (Direct Buy)
    public function direct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::with('seller')->findOrFail($request->product_id);
        $quantity = $request->quantity;

        // Kita memalsukan struktur data biar mirip sama Cart Item
        // Jadi view checkout.blade.php gak perlu diubah banyak
        $fakeCartItem = (object) [
            'id' => null, // Gak ada ID keranjang
            'product_id' => $product->id,
            'product' => $product,
            'quantity' => $quantity,
            'price' => $product->price
        ];

        $items = collect([$fakeCartItem]);
        $subtotal = $product->price * $quantity;
        
        // Kirim data dengan flag 'source' = 'direct'
        return view('buyer.checkout', [
            'items' => $items,
            'totalWeight' => 0, // Implementasi berat nanti
            'subtotal' => $subtotal,
            'source' => 'direct',
            'cart_ids' => [] // Kosong karena bukan dari keranjang
        ]);
    }

    // PROSES BAYAR (STORE ORDER)
    public function store(Request $request)
    {
        // ... Validasi alamat, pengiriman, dll ...

        DB::transaction(function () use ($request) {
            // 1. Simpan Data Order Utama
            // ... (Logic simpan order header) ...
            
            // 2. Simpan Order Items
            // ... (Logic simpan order details) ...

            // 3. 🔥 PEMBERSIHAN KERANJANG (CRUCIAL!) 🔥
            // Hanya hapus jika sumbernya dari 'cart' DAN transaksinya berhasil
            if ($request->source === 'cart' && $request->has('cart_ids')) {
                Cart::whereIn('id', $request->cart_ids)
                    ->where('user_id', Auth::id())
                    ->delete();
            }
        });

        return redirect()->route('buyer.orders')->with('success', 'Pesanan berhasil dibuat!');
    }
}