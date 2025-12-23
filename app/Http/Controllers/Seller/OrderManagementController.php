<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    /**
     * Menampilkan Daftar Pesanan Masuk ke Toko
     */
    public function index()
    {
        $orders = Order::where('seller_id', auth()->id())
            ->with(['buyer', 'items.product'])
            ->latest()
            ->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    /**
     * Menampilkan Detail Pesanan (Untuk melihat bukti bayar & alamat lengkap)
     */
    public function show($id)
    {
        // Cari order berdasarkan ID, sekalian ambil data pembeli & barangnya
        $order = Order::with(['buyer', 'items.product'])
            ->findOrFail($id);

        // Validasi Keamanan: Pastikan order ini milik toko si Seller yang login
        if ($order->seller_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Ini bukan pesanan toko Anda.');
        }

        return view('seller.orders.show', compact('order'));
    }

    /**
     * Update Status Pesanan (Processing, Shipped, Cancelled)
     */
    public function updateStatus(Request $request, Order $order)
    {
        // 1. Validasi Keamanan
        if ($order->seller_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        // 2. Validasi Input
        $request->validate([
            'status' => 'required|in:processing,shipped,cancelled,payment_rejected',
            'tracking_number' => 'required_if:status,shipped|nullable|string|min:5',
            'cancellation_reason' => 'required_if:status,cancelled,payment_rejected|nullable|string|max:255',
            'cancellation_note' => 'nullable|string|max:500',
        ], [
            'tracking_number.required_if' => 'Nomor resi wajib diisi saat mengirim pesanan.',
            'tracking_number.min' => 'Nomor resi minimal 5 karakter.',
            'cancellation_reason.required_if' => 'Alasan pembatalan/penolakan wajib diisi.',
        ]);

        $newStatus = $request->status;
        $oldStatus = $order->status;

        // Gunakan database transaction untuk keamanan data
        DB::beginTransaction();
        
        try {
            // 3. Logika Potong Stok (Jika Dikirim)
            if ($newStatus === 'shipped' && $oldStatus !== 'shipped') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        // Cek apakah stok mencukupi
                        if ($product->stock < $item->quantity) {
                            throw new \Exception("Stok produk {$product->name} tidak mencukupi.");
                        }
                        $product->decrement('stock', $item->quantity);
                    }
                }
            }

            // 4. Kembalikan Stok (Jika Dibatalkan atau Payment Ditolak)
            if (in_array($newStatus, ['cancelled', 'payment_rejected']) && $oldStatus === 'shipped') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            // 5. Siapkan Data Update
            $updateData = ['status' => $newStatus];

            // Jika status shipped, simpan tracking number
            if ($newStatus === 'shipped') {
                $updateData['tracking_number'] = trim($request->tracking_number);
                
                // Log untuk debugging
                Log::info('Tracking number disimpan:', [
                    'tracking_number' => $request->tracking_number,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
            }

            // Jika status cancelled atau payment_rejected, simpan alasan
            if (in_array($newStatus, ['cancelled', 'payment_rejected'])) {
                $updateData['cancellation_reason'] = $request->cancellation_reason;
                $updateData['cancellation_note'] = $request->cancellation_note;
            }

            // 6. Update Order
            $order->update($updateData);

            // 7. KIRIM NOTIFIKASI KE BUYER 🔔
            $this->sendNotificationToBuyer($order, $newStatus, $request);

            // Commit transaction jika semua berhasil
            DB::commit();

            return back()->with('success', 'Status pesanan berhasil diperbarui & notifikasi dikirim ke pembeli!');
            
        } catch (\Exception $e) {
            // Rollback jika ada error
            DB::rollBack();
            
            Log::error('Error update order status:', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Kirim Notifikasi ke Buyer
     */
    private function sendNotificationToBuyer($order, $status, $request)
    {
        $title = 'Update Pesanan';
        $message = '';
        
        switch ($status) {
            case 'processing':
                $title = 'Pesanan Diproses';
                $message = "Penjual telah menerima pesanan Anda #{$order->order_number} dan sedang menyiapkannya.";
                break;
                
            case 'shipped':
                $title = 'Pesanan Dikirim 🚚';
                $message = "Hore! Paket #{$order->order_number} sudah dikirim. Nomor Resi: {$request->tracking_number}";
                break;
                
            case 'cancelled':
                $title = 'Pesanan Dibatalkan ❌';
                $message = "Maaf, pesanan #{$order->order_number} dibatalkan oleh penjual. Alasan: {$request->cancellation_reason}";
                break;
                
            case 'payment_rejected':
                $title = 'Pembayaran Ditolak ⚠️';
                $message = "Bukti pembayaran pesanan #{$order->order_number} ditolak. Alasan: {$request->cancellation_reason}. Silakan upload ulang bukti yang benar.";
                break;
                
            default:
                $message = "Status pesanan #{$order->order_number} telah diperbarui.";
        }

        // Simpan ke Database Notifikasi
        Notification::create([
            'user_id' => $order->buyer_id,
            'type'    => 'order_status',
            'title'   => $title,
            'message' => $message,
            'link'    => route('buyer.orders.show', $order->id),
            'is_read' => false,
        ]);

        Log::info('Notifikasi dikirim ke buyer:', [
            'buyer_id' => $order->buyer_id,
            'order_id' => $order->id,
            'status' => $status
        ]);
    }
}