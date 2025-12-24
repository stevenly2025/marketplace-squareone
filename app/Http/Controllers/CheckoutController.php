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
        try {
            return $this->processDirectCheckout($request);
        } catch (\Exception $e) {
            Log::error('Direct checkout error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman pembayaran dengan LOGIC ONGKIR & PROMO
     */
    public function index(Request $request)
    {
        try {
            return $this->processCartCheckout($request);
        } catch (\Exception $e) {
            Log::error('Cart checkout error', ['error' => $e->getMessage()]);
            return redirect()->route('buyer.cart.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memproses Pesanan (Simpan Logic Ongkir ke Database)
     */
    public function store(Request $request)
    {
        try {
            $this->logCheckoutStart($request);
            
            $request->validate([
                'seller_id' => 'required|exists:users,id',
                'payment_proof' => 'required|image|max:2048',
            ]);

            return DB::transaction(function () use ($request) {
                return $this->processOrder($request);
            });
        } catch (\Exception $e) {
            Log::error('Checkout store error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Buyer menyelesaikan pesanan (status shipped -> completed)
     */
    public function complete($id)
    {
        try {
            return $this->processOrderCompletion($id);
        } catch (\Exception $e) {
            Log::error('Complete order error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Buyer membatalkan pesanan (hanya jika status pending/processing)
     */
    public function cancel(Request $request, $id)
    {
        try {
            return $this->processOrderCancellation($request, $id);
        } catch (\Exception $e) {
            Log::error('Cancel order error', ['error' => $e->getMessage()]);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * ============================================
     * PRIVATE HELPER METHODS
     * ============================================
     */

    /**
     * Process direct checkout
     */
    private function processDirectCheckout(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        if (!$productId || $quantity < 1) {
            return redirect()->back()->with('error', 'Data produk tidak valid.');
        }

        $product = Product::with('seller')->findOrFail($productId);

        if ($product->stock < $quantity) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }

        // Buat fake cart item
        $item = new Cart();
        $item->id = 'direct_' . $productId;
        $item->product_id = $product->id;
        $item->product = $product;
        $item->quantity = $quantity;
        $item->buyer_id = auth()->id();
        $item->seller_id = $product->seller_id;

        $cartItems = collect([$item]);
        $seller = $product->seller;
        $subtotal = $product->price * $quantity;

        // Hitung ongkir dan diskon
        $shippingData = $this->calculateShippingCost(auth()->user()->city, $seller->city);
        $promoData = $this->calculatePromo($subtotal, $shippingData['cost']);
        
        $totalAmount = $subtotal + $shippingData['cost'] - $promoData['discount'];

        return view('buyer.checkout.index', [
            'seller' => $seller,
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingData['cost'],
            'shippingType' => $shippingData['type'],
            'discount' => $promoData['discount'],
            'promoMessage' => $promoData['message'],
            'totalAmount' => $totalAmount,
            'is_direct_checkout' => true
        ]);
    }

    /**
     * Process cart checkout
     */
    private function processCartCheckout(Request $request)
    {
        $cartIds = $request->input('cart_ids');

        if (!$cartIds || !is_array($cartIds) || count($cartIds) == 0) {
            return redirect()->route('buyer.cart.index')->with('error', 'Silakan pilih minimal satu barang untuk di-checkout.');
        }

        $cartItems = Cart::whereIn('id', $cartIds)
            ->where('buyer_id', auth()->id())
            ->with('product.seller')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('buyer.cart.index')->with('error', 'Keranjang kosong.');
        }

        $seller = $cartItems->first()->product->seller;
        $subtotal = $this->calculateCartSubtotal($cartItems);

        // Hitung ongkir dan diskon
        $shippingData = $this->calculateShippingCost(auth()->user()->city, $seller->city);
        $promoData = $this->calculatePromo($subtotal, $shippingData['cost']);
        
        $totalAmount = $subtotal + $shippingData['cost'] - $promoData['discount'];

        return view('buyer.checkout.index', [
            'seller' => $seller,
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingData['cost'],
            'shippingType' => $shippingData['type'],
            'discount' => $promoData['discount'],
            'promoMessage' => $promoData['message'],
            'totalAmount' => $totalAmount,
            'is_direct_checkout' => false
        ]);
    }

    /**
     * Log checkout start
     */
    private function logCheckoutStart(Request $request)
    {
        Log::info('=== CHECKOUT STORE DEBUG - START ===', [
            'timestamp' => now()->toDateTimeString(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
        ]);
    }

    /**
     * Process order creation
     */
    private function processOrder(Request $request)
    {
        $sellerId = $request->seller_id;
        $isDirect = $this->isDirectCheckoutMode($request);
        
        Log::info('=== MODE DETECTION ===', ['isDirect' => $isDirect]);

        if ($isDirect) {
            $orderData = $this->prepareDirectOrderData($request);
        } else {
            $orderData = $this->prepareCartOrderData($sellerId);
        }

        $seller = User::findOrFail($sellerId);
        $shippingData = $this->calculateShippingCost(auth()->user()->city, $seller->city);
        $promoData = $this->calculatePromo($orderData['subtotal'], $shippingData['cost']);
        
        $totalAmount = $orderData['subtotal'] + $shippingData['cost'] - $promoData['discount'];

        Log::info('Final Amount Calculation', [
            'subtotal' => $orderData['subtotal'],
            'shipping_cost' => $shippingData['cost'],
            'discount' => $promoData['discount'],
            'total_amount' => $totalAmount,
        ]);

        $paymentProofPath = $this->uploadPaymentProof($request);
        $order = $this->createOrder($sellerId, $totalAmount, $paymentProofPath, $shippingData, $promoData['discount']);
        $this->createOrderItems($order, $orderData['items']);
        $this->sendNotification($order);
        
        if (!$isDirect) {
            $this->clearCart($sellerId);
        }

        Log::info('=== CHECKOUT STORE DEBUG - SUCCESS ===', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'mode' => $isDirect ? 'DIRECT CHECKOUT' : 'CART CHECKOUT',
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('buyer.orders')->with('success', 'Pesanan berhasil dibuat! Menunggu konfirmasi dari penjual.');
    }

    /**
     * Check if direct checkout mode
     */
    private function isDirectCheckoutMode(Request $request): bool
    {
        return $request->has('direct_product_id') && $request->has('direct_quantity');
    }

    /**
     * Prepare direct order data
     */
    private function prepareDirectOrderData(Request $request): array
    {
        Log::info('=== DIRECT CHECKOUT MODE ACTIVATED ===');
        
        $productId = $request->input('direct_product_id');
        $quantity = (int) $request->input('direct_quantity');
        
        $product = Product::with('seller')->findOrFail($productId);
        
        if ($product->stock < $quantity) {
            Log::warning('Direct Mode - Insufficient Stock', [
                'available_stock' => $product->stock,
                'requested_quantity' => $quantity,
            ]);
            throw new \Exception('Stok tidak mencukupi.');
        }
        
        $subtotal = $product->price * $quantity;
        
        return [
            'subtotal' => $subtotal,
            'items' => [[
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ]]
        ];
    }

    /**
     * Prepare cart order data
     */
    private function prepareCartOrderData(int $sellerId): array
    {
        Log::info('=== CART CHECKOUT MODE ACTIVATED ===');
        
        $cartItems = Cart::where('buyer_id', auth()->id())
            ->where('seller_id', $sellerId)
            ->with('product')
            ->get();
        
        if ($cartItems->isEmpty()) {
            Log::warning('Cart Mode - Cart is Empty');
            throw new \Exception('Keranjang kosong.');
        }
        
        $subtotal = $this->calculateCartSubtotal($cartItems);
        
        $items = $cartItems->map(function($item) {
            return [
                'product' => $item->product,
                'quantity' => $item->quantity,
                'subtotal' => $item->product->price * $item->quantity
            ];
        })->toArray();
        
        return [
            'subtotal' => $subtotal,
            'items' => $items
        ];
    }

    /**
     * Calculate cart subtotal
     */
    private function calculateCartSubtotal($cartItems): float
    {
        return $cartItems->sum(function($item) {
            return $item->quantity * $item->product->price;
        });
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShippingCost(string $buyerCity, string $sellerCity): array
    {
        $buyerCityData = City::where('name', $buyerCity)->first();
        $sellerCityData = City::where('name', $sellerCity)->first();

        if (!$buyerCityData || !$sellerCityData) {
            return [
                'cost' => 50000,
                'type' => 'Flat Rate (Data Lokasi Belum Lengkap)'
            ];
        }

        if ($buyerCity === $sellerCity) {
            return [
                'cost' => 10000,
                'type' => 'Tier 1: Kurir Instan (Satu Kota)'
            ];
        }

        if ($buyerCityData->island === $sellerCityData->island) {
            return [
                'cost' => 25000,
                'type' => 'Tier 2: Kargo Darat (Satu Pulau)'
            ];
        }

        return [
            'cost' => 50000,
            'type' => 'Tier 3: Kargo Laut/Udara (Beda Pulau)'
        ];
    }

    /**
     * Calculate promo discount
     */
    private function calculatePromo(float $subtotal, float $shippingCost): array
    {
        $discount = 0;
        $message = null;

        $userCreatedAt = auth()->user()->created_at;
        $daysSinceCreated = $userCreatedAt->diffInDays(now());
        $isNewUser = $daysSinceCreated <= 7;
        $orderCount = Order::where('buyer_id', auth()->id())->count();

        if ($isNewUser && $orderCount < 2 && $subtotal >= 50000 && $shippingCost < 50000) {
            $discount = 10000;
            $message = '🎉 Promo Pengguna Baru Diaktifkan!';
        }

        return [
            'discount' => $discount,
            'message' => $message
        ];
    }

    /**
     * Upload payment proof
     */
    private function uploadPaymentProof(Request $request): string
    {
        Log::info('=== UPLOADING PAYMENT PROOF ===');
        
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');
        
        Log::info('Payment Proof Uploaded', ['path' => $path]);
        
        return $path;
    }

    /**
     * Create order
     */
    private function createOrder(int $sellerId, float $totalAmount, string $paymentProofPath, array $shippingData, float $discount): Order
    {
        Log::info('=== CREATING ORDER ===');
        
        $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
        
        $order = Order::create([
            'order_number' => $orderNumber,
            'buyer_id' => auth()->id(),
            'seller_id' => $sellerId,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_proof' => $paymentProofPath,
            'shipping_address' => auth()->user()->address . ' (' . auth()->user()->city . ')',
            'shipping_phone' => auth()->user()->phone,
            'shipping_cost' => $shippingData['cost'],
            'shipping_type' => $shippingData['type'],
            'discount' => $discount,
        ]);
        
        Log::info('✅ Order Created Successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
        
        return $order;
    }

    /**
     * Create order items
     */
    private function createOrderItems(Order $order, array $items): void
    {
        Log::info('=== CREATING ORDER ITEMS ===', [
            'items_to_create' => count($items),
        ]);
        
        foreach ($items as $index => $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];
            
            if ($product->stock < $quantity) {
                throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
            }
            
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->image,
                'price' => $product->price,
                'quantity' => $quantity,
                'subtotal' => $item['subtotal'],
            ]);
            
            // Kurangi stok produk
            $product->decrement('stock', $quantity);
        }
    }

    /**
     * Send notification to seller
     */
    private function sendNotification(Order $order): void
    {
        Log::info('=== SENDING NOTIFICATION ===');
        
        Notification::create([
            'user_id' => $order->seller_id,
            'type' => 'new_order',
            'title' => 'Pesanan Baru Masuk! 🎉',
            'message' => "Ada pesanan baru #{$order->order_number} dari " . auth()->user()->name . " senilai Rp " . number_format($order->total_amount, 0, ',', '.'),
            'link' => route('seller.orders.show', $order->id),
            'is_read' => false,
        ]);
    }

    /**
     * Clear cart items
     */
    private function clearCart(int $sellerId): void
    {
        Log::info('=== CLEARING CART (Cart Mode) ===');
        
        $deletedCount = Cart::where('buyer_id', auth()->id())
            ->where('seller_id', $sellerId)
            ->delete();
        
        Log::info('✅ Cart Cleared', ['deleted_items' => $deletedCount]);
    }

    /**
     * Process order completion
     */
    private function processOrderCompletion($id)
    {
        $order = Order::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->firstOrFail();
        
        if ($order->status !== 'shipped') {
            return back()->with('error', 'Pesanan tidak bisa diselesaikan. Status pesanan harus "Sedang Dikirim".');
        }
        
        $order->update(['status' => 'completed']);

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
        ]);
        
        return back()->with('success', 'Terima kasih! Pesanan telah selesai. Jangan lupa berikan review! ⭐');
    }

    /**
     * Process order cancellation
     */
    private function processOrderCancellation(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:255',
            'cancellation_note' => 'nullable|string|max:500',
        ]);

        $order = Order::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->firstOrFail();
        
        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Pesanan tidak bisa dibatalkan. Hubungi penjual untuk pembatalan.');
        }

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancellation_note' => $request->cancellation_note,
        ]);

        // Kembalikan stok
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

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
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }
}