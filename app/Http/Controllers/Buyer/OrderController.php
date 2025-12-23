<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan milik pembeli yang sedang login.
     */
    public function index()
    {
        // Ambil data order milik user yang login, urutkan dari yang terbaru
        $orders = Order::where('buyer_id', auth()->id())
            ->with(['seller', 'items']) // Load data penjual & item barang
            ->latest()
            ->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    /**
     * Menampilkan Detail Pesanan
     */
    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->with(['items.product', 'seller'])
            ->firstOrFail();

        return view('buyer.orders.show', compact('order'));
    }

    /**
     * Logic Pembatalan (Buyer)
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->firstOrFail();

        // Guardrail: Cek Waktu & Status
        // Boleh batal jika < 24 jam DAN status masih pending
        if ($order->created_at->diffInHours(now()) > 24) {
            return back()->with('error', 'Pesanan tidak bisa dibatalkan karena sudah lebih dari 24 jam.');
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan sudah diproses/dikirim, tidak bisa dibatalkan.');
        }

        // Validasi input
        $request->validate([
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:500',
        ]);

        // Update Status & Alasan
        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->reason,
            'cancellation_note' => $request->note,
        ]);

        // Kirim Notifikasi ke Seller
        Notification::create([
            'user_id' => $order->seller_id,
            'type' => 'order_cancelled',
            'title' => 'Pesanan Dibatalkan oleh Pembeli',
            'message' => "Pembeli membatalkan pesanan #{$order->order_number}. Alasan: {$request->reason}",
            'link' => route('seller.orders.show', $order->id),
            'is_read' => false,
        ]);

        Log::info('Order cancelled by buyer', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'reason' => $request->reason
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Logic Upload Ulang Bukti (Jika ditolak/salah)
     */
    public function reupload(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $order = Order::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->firstOrFail();

        // Hanya boleh reupload jika status pending atau payment_rejected
        if (!in_array($order->status, ['pending', 'payment_rejected'])) {
            return back()->with('error', 'Tidak bisa upload ulang saat ini.');
        }

        // Hapus bukti lama jika ada
        if ($order->payment_proof && Storage::disk('public')->exists($order->payment_proof)) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        // Upload bukti baru
        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'status' => 'pending', // Reset jadi pending biar dicek lagi sama seller
        ]);

        // Kirim Notifikasi ke Seller
        Notification::create([
            'user_id' => $order->seller_id,
            'type' => 'payment_reuploaded',
            'title' => 'Bukti Pembayaran Diupload Ulang',
            'message' => "Pembeli mengupload ulang bukti pembayaran untuk pesanan #{$order->order_number}. Silakan cek kembali.",
            'link' => route('seller.orders.show', $order->id),
            'is_read' => false,
        ]);

        Log::info('Payment proof reuploaded', [
            'order_id' => $order->id,
            'order_number' => $order->order_number
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diupload ulang.');
    }

    /**
     * Hapus Riwayat Pesanan (Hanya untuk pesanan yang sudah selesai/dibatalkan)
     */
    public function destroy($id)
    {
        $order = Order::where('id', $id)
            ->where('buyer_id', auth()->id())
            ->firstOrFail();
        
        // Guardrail: Hanya boleh hapus jika sudah selesai/batal
        if (!in_array($order->status, ['completed', 'cancelled', 'payment_rejected'])) {
            return back()->with('error', 'Pesanan yang masih aktif tidak bisa dihapus.');
        }

        // Simpan info untuk logging
        $orderNumber = $order->order_number;
        
        // Hapus order
        $order->delete();

        Log::info('Order deleted by buyer', [
            'order_id' => $id,
            'order_number' => $orderNumber,
            'buyer_id' => auth()->id()
        ]);

        return back()->with('success', 'Riwayat pesanan berhasil dihapus.');
    }
}