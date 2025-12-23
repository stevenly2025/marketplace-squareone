<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // Menampilkan isi keranjang pembeli
    public function index()
    {
        // Ambil cart milik buyer yang login
        // Kita load relasi: product, dan seller-nya product
        $cartItems = Cart::with(['product.seller'])
            ->where('buyer_id', auth()->id())
            ->latest()
            ->get();

        return view('buyer.cart.index', compact('cartItems'));
    }

    // Tambahkan barang ke keranjang
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        // Cek stok dulu
        if ($product->stock < 1) {
            return back()->with('error', 'Stok habis bro!');
        }

        // Default quantity = 1 jika tidak ada input
        $quantity = $request->input('quantity', 1);

        // Validasi quantity tidak melebihi stok
        if ($quantity > $product->stock) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia!');
        }

        // Cari apakah barang ini sudah ada di keranjang user?
        $existingCart = Cart::where('buyer_id', auth()->id())
            ->where('product_id', $productId)
            ->first();

        if ($existingCart) {
            // Update quantity kalau sudah ada
            $newQuantity = $existingCart->quantity + $quantity;
            
            // Validasi total quantity tidak melebihi stok
            if ($newQuantity > $product->stock) {
                return back()->with('error', 'Total jumlah melebihi stok yang tersedia!');
            }
            
            $existingCart->update([
                'quantity' => $newQuantity
            ]);
        } else {
            // Buat baru kalau belum ada
            Cart::create([
                'buyer_id'       => auth()->id(),
                'product_id'     => $productId,
                'seller_id'      => $product->seller_id,
                'quantity'       => $quantity,
                'price_snapshot' => $product->price,
            ]);
        }

        // LOGIKA PINDAH HALAMAN (Redirect)
        // Jika tombol "Beli Sekarang" ditekan
        if ($request->input('action') === 'buy_now') {
            return redirect()->route('buyer.checkout', ['seller_id' => $product->seller_id]);
        }

        // Jika tombol "Masukkan Keranjang" ditekan
        return back()->with('success', 'Barang masuk keranjang! 🛒');
    }

    // Update quantity di keranjang
    public function update(Request $request, $id)
    {
        // Cari cart berdasarkan ID dan validasi ownership
        $cart = Cart::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->firstOrFail();

        // Validasi input
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        // Validasi stok produk
        if ($request->quantity > $cart->product->stock) {
            return back()->with('error', 'Stok tidak cukup!');
        }

        // Update quantity
        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Jumlah berhasil diubah.');
    }

    // Hapus item dari keranjang
    public function destroy($id)
    {
        // Cari cart berdasarkan ID dan validasi ownership
        $cart = Cart::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->firstOrFail();

        $cart->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}