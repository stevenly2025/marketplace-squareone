<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Library PDF

class ReportController extends Controller
{
    // 1. Tampilkan Halaman Filter (Pilih Tanggal)
    public function index()
    {
        return view('seller.reports.index');
    }

    // 2. Proses Export ke PDF
    public function export(Request $request)
    {
        // Validasi input tanggal
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        // Ambil data pesanan sesuai tanggal & seller yang login
        $orders = Order::where('seller_id', auth()->id())
            ->where('status', 'completed') // Hanya yang sudah selesai (dana masuk)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['buyer', 'items'])
            ->latest()
            ->get();

        // Hitung Total Omzet di periode tersebut
        $totalOmzet = $orders->sum('total_amount');

        // Siapkan data untuk dikirim ke view PDF
        $data = [
            'title' => 'Laporan Penjualan Toko',
            'date' => date('d M Y'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'orders' => $orders,
            'total_omzet' => $totalOmzet,
            'seller_name' => auth()->user()->name
        ];

        // Load View PDF dan Download
        $pdf = Pdf::loadView('seller.reports.pdf', $data);
        
        // Nama file saat didownload
        $fileName = 'Laporan-Penjualan-SquareOne-' . date('YmdHis') . '.pdf';
        return $pdf->download($fileName);
    }
}