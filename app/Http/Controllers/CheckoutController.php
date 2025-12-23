<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\City;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    /**
     * ✅ BARU: Direct Checkout untuk "Beli Sekarang"
     */
    public function direct(Request $request)
    {
        // 1. Ambil data dari form Beli Sekarang
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        // 2. Validasi input
        if (!$productId || $quantity < 1) {
            return redirect()->back()->with('error', 'Data produk tidak valid.');
        }

        // 3. Ambil data produk dengan seller
        $product = Product::with('seller')->findOrFail($productId);

        // 4. Validasi stok
        if ($product->stock < $quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }

        // 5. Buat "Fake" Cart Item supaya kompatibel dengan view checkout
        $item = new Cart();
        $item->id = 'direct_' . $productId; // ID sementara untuk direct checkout
        $item->product_id = $product->id;
        $item->product = $product;
        $item->quantity = $quantity;
        $item->buyer_id = auth()->id();
        $item->seller_id = $product->seller_id;

        // 6. Masukkan ke collection
        $cartItems = collect([$item]);
        $seller = $product->seller;

        // 7. Hitung Subtotal
        $subtotal = $product->price * $quantity;

        // 8. LOGIC ONGKIR 3 LAPIS (Tiering) - SAMA DENGAN METHOD INDEX
        $buyerCityName = auth()->user()->city;
        $sellerCityName = $seller->city;
        
        $buyerCityData = City::where('name', $buyerCityName)->first();
        $sellerCityData = City::where('name', $sellerCityName)->first();

        $shippingCost = 0;
        $shippingType = '';

        if (!$buyerCityData || !$sellerCityData) {
            $shippingCost = 50000; 
            $shippingType = 'Flat Rate (Data Lokasi Belum Lengkap)';
        } 
        elseif ($buyerCityName == $sellerCityName) {
            $shippingCost = 10000;
            $shippingType = 'Tier 1: Kurir Instan (Satu Kota)';
        } 
        elseif ($buyerCityData->island == $sellerCityData->island) {
            $shippingCost = 25000;
            $shippingType = 'Tier 2: Kargo Darat (Satu Pulau)';
        } 
        else {
            $shippingCost = 50000;
            $shippingType = 'Tier 3: Kargo Laut/Udara (Beda Pulau)';
        }

        // 9. LOGIC PROMO USER BARU
        $discount = 0;
        $promoMessage = null;

        $isNewUser = auth()->user()->created_at->diffInDays(now()) <= 7;
        $orderCount = Order::where('buyer_id', auth()->id())->count();

        if ($isNewUser && $orderCount < 2 && $subtotal >= 50000 && $shippingCost < 50000) {
            $discount = 10000;
            $promoMessage = '🎉 Promo Pengguna Baru Diaktifkan!';
        }

        // 10. Hitung Total Akhir
        $totalAmount = $subtotal + $shippingCost - $discount;

        // 11. Kirim ke view checkout
        return view('buyer.checkout.index', compact(
            'seller', 'cartItems', 'subtotal', 'shippingCost', 
            'shippingType', 'discount', 'promoMessage', 'totalAmount'
        ));
    }

    /**
     * Menampilkan halaman pembayaran dengan LOGIC ONGKIR & PROMO
     * ✅ DIPERBAIKI: Hapus parameter $seller_id
     */
    public function index(Request $request)
    {
        // Ambil ID barang yang dicentang dari keranjang
        $cartIds = $request->input('cart_ids');

        // JIKA cartIds kosong (berarti user akses lewat URL langsung/GET), 
        // arahkan balik ke keranjang biar mereka milih barang dulu.
        if (!$cartIds || !is_array($cartIds) || count($cartIds) == 0) {
            return redirect()->route('buyer.cart.index')->with('error', 'Silakan pilih minimal satu barang untuk di-checkout.');
        }

        // Ambil data produk berdasarkan ID yang dicentang
        $cartItems = Cart::whereIn('id', $cartIds)
            ->where('buyer_id', auth()->id())
            ->with('product.seller')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'Keranjang kosong.');
        }

        // Ambil Seller (asumsi checkout per satu seller)
        $seller = $cartItems->first()->product->seller;

        // 1. Hitung Subtotal Belanja
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);

        // 2. LOGIC ONGKIR 3 LAPIS (Tiering)
        $buyerCityName = auth()->user()->city;
        $sellerCityName = $seller->city;
        
        // Cari data pulau berdasarkan nama kota
        $buyerCityData = City::where('name', $buyerCityName)->first();
        $sellerCityData = City::where('name', $sellerCityName)->first();

        $shippingCost = 0;
        $shippingType = '';

        // Jika data kota belum lengkap (salah satu kosong), kasih tarif default
        if (!$buyerCityData || !$sellerCityData) {
            $shippingCost = 50000; 
            $shippingType = 'Flat Rate (Data Lokasi Belum Lengkap)';
        } 
        // Tier 1: Satu Kota
        elseif ($buyerCityName == $sellerCityName) {
            $shippingCost = 10000;
            $shippingType = 'Tier 1: Kurir Instan (Satu Kota)';
        } 
        // Tier 2: Beda Kota, TAPI Satu Pulau
        elseif ($buyerCityData->island == $sellerCityData->island) {
            $shippingCost = 25000;
            $shippingType = 'Tier 2: Kargo Darat (Satu Pulau)';
        } 
        // Tier 3: Beda Pulau
        else {
            $shippingCost = 50000;
            $shippingType = 'Tier 3: Kargo Laut/Udara (Beda Pulau)';
        }

        // 3. LOGIC PROMO USER BARU
        $discount = 0;
        $promoMessage = null;

        $isNewUser = auth()->user()->created_at->diffInDays(now()) <= 7; // Daftar < 7 hari
        $orderCount = Order::where('buyer_id', auth()->id())->count(); // Belum pernah belanja banyak

        // Syarat: User Baru + Belanja Min 50rb + Bukan Pengiriman Tier 3 (Mahal)
        if ($isNewUser && $orderCount < 2 && $subtotal >= 50000 && $shippingCost < 50000) {
            $discount = 10000;
            $promoMessage = '🎉 Promo Pengguna Baru Diaktifkan!';
        }

        // Hitung Total Akhir
        $totalAmount = $subtotal + $shippingCost - $discount;

        return view('buyer.checkout.index', compact(
            'seller', 'cartItems', 'subtotal', 'shippingCost', 'shippingType', 'discount', 'promoMessage', 'totalAmount'
        ));
    }

    /**
     * Memproses Pesanan (Simpan Logic Ongkir ke Database)
     */
    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:users,id',
            'payment_proof' => 'required|image|max:2048',
        ]);

        return DB::transaction(function () use ($request) {
            $seller_id = $request->seller_id;
            
            // Ambil cart items
            $cartItems = Cart::where('buyer_id', auth()->id())
                ->where('seller_id', $seller_id)
                ->with('product')
                ->get();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('buyer.cart.index')->with('error', 'Keranjang kosong.');
            }
            
            $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->price);
            
            // --- COPY LOGIC ONGKIR & DISKON (Backend Validation) ---
            $seller = User::findOrFail($seller_id);
            $buyerCityData = City::where('name', auth()->user()->city)->first();
            $sellerCityData = City::where('name', $seller->city)->first();
            
            $shippingCost = 50000; // Default
            $shippingType = 'Flat Rate (Data Lokasi Belum Lengkap)';
            
            if ($buyerCityData && $sellerCityData) {
                if (auth()->user()->city == $seller->city) {
                    $shippingCost = 10000;
                    $shippingType = 'Tier 1: Kurir Instan (Satu Kota)';
                } elseif ($buyerCityData->island == $sellerCityData->island) {
                    $shippingCost = 25000;
                    $shippingType = 'Tier 2: Kargo Darat (Satu Pulau)';
                } else {
                    $shippingCost = 50000;
                    $shippingType = 'Tier 3: Kargo Laut/Udara (Beda Pulau)';
                }
            }
            
            // Logic Diskon
            $discount = 0;
            $isNewUser = auth()->user()->created_at->diffInDays(now()) <= 7;
            $orderCount = Order::where('buyer_id', auth()->id())->count();
            
            if ($isNewUser && $orderCount < 2 && $subtotal >= 50000 && $shippingCost < 50000) {
                $discount = 10000;
            }
            
            $totalAmount = $subtotal + $shippingCost - $discount;
            // -----------------------------------------------------------

            // Upload Payment Proof
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

            // Simpan Order
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . rand(1000, 9999),
                'buyer_id' => auth()->id(),
                'seller_id' => $seller_id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_proof' => $paymentProofPath,
                'shipping_address' => auth()->user()->address . ' (' . auth()->user()->city . ')',
                'shipping_phone' => auth()->user()->phone,
            ]);

            // Pindahkan Item dari Cart ke OrderItem
            foreach ($cartItems as $item) {
                // Validasi stok
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception("Stok produk {$item->product->name} tidak mencukupi.");
                }
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->image,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->product->price * $item->quantity,
                ]);
                
                // JANGAN potong stok di sini, tunggu sampai seller kirim barang
                // $item->product->decrement('stock', $item->quantity);
            }

            // Kirim Notifikasi ke Seller
            Notification::create([
                'user_id' => $seller_id,
                'type' => 'new_order',
                'title' => 'Pesanan Baru Masuk! 🎉',
                'message' => "Ada pesanan baru #{$order->order_number} dari " . auth()->user()->name . " senilai Rp " . number_format($totalAmount, 0, ',', '.'),
                'link' => route('seller.orders.show', $order->id),
                'is_read' => false,
            ]);

            // Hapus Cart setelah order dibuat
            Cart::where('buyer_id', auth()->id())->where('seller_id', $seller_id)->delete();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'buyer_id' => auth()->id(),
                'seller_id' => $seller_id,
                'total_amount' => $totalAmount
            ]);

            return redirect()->route('buyer.orders')->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi dari penjual.');
        });
    }

    /**
     * Buyer menyelesaikan pesanan (status shipped -> completed)
     */
    public function complete($id)
    {
        try {
            // Cari order milik buyer yang login
            $order = Order::where('id', $id)
                ->where('buyer_id', auth()->id())
                ->firstOrFail();
            
            // Validasi: Hanya boleh selesai kalau statusnya SHIPPED
            if ($order->status !== 'shipped') {
                return back()->with('error', 'Pesanan tidak bisa diselesaikan. Status pesanan harus "Sedang Dikirim".');
            }
            
            // Update status ke completed
            $order->update(['status' => 'completed']);

            // Kirim Notifikasi ke Seller
            Notification::create([
                'user_id' => $order->seller_id,
                'type' => 'order_completed',
                'title' => 'Pesanan Selesai ✅',
                'message' => "Pembeli telah mengkonfirmasi pesanan #{$order->order_number} selesai. Transaksi berhasil!",
                'link' => route('seller.orders.show', $order->id),
                'is_read' => false,
            ]);

            Log::info('Order completed by buyer', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'buyer_id' => auth()->id()
            ]);
            
            return back()->with('success', 'Terima kasih! Pesanan telah selesai. Jangan lupa berikan review! ⭐');
            
        } catch (\Exception $e) {
            Log::error('Error completing order', [
                'order_id' => $id,
                'buyer_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Buyer membatalkan pesanan (hanya jika status pending/processing)
     */
    public function cancel(Request $request, $id)
    {
        try {
            $order = Order::where('id', $id)
                ->where('buyer_id', auth()->id())
                ->firstOrFail();
            
            // Validasi: Hanya boleh dibatalkan jika status pending atau processing
            if (!in_array($order->status, ['pending', 'processing'])) {
                return back()->with('error', 'Pesanan tidak bisa dibatalkan. Hubungi penjual untuk pembatalan.');
            }

            $request->validate([
                'cancellation_reason' => 'required|string|max:255',
                'cancellation_note' => 'nullable|string|max:500',
            ]);

            // Update status dan alasan
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancellation_note' => $request->cancellation_note,
            ]);

            // Kembalikan stok jika sudah dipotong
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            // Kirim Notifikasi ke Seller
            Notification::create([
                'user_id' => $order->seller_id,
                'type' => 'order_cancelled',
                'title' => 'Pesanan Dibatalkan',
                'message' => "Pembeli membatalkan pesanan #{$order->order_number}. Alasan: {$request->cancellation_reason}",
                'link' => route('seller.orders.show', $order->id),
                'is_read' => false,
            ]);

            Log::info('Order cancelled by buyer', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'reason' => $request->cancellation_reason
            ]);

            return back()->with('success', 'Pesanan berhasil dibatalkan.');
            
        } catch (\Exception $e) {
            Log::error('Error cancelling order', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}