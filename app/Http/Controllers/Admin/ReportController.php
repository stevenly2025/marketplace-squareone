<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function export()
    {
        // Ambil SEMUA order dari SEMUA toko
        $orders = Order::with(['buyer', 'seller'])->latest()->get();

        $data = [
            'title' => 'Laporan Global SquareOne (Admin)',
            'date' => date('d/m/Y'),
            'orders' => $orders,
            'total_omzet' => $orders->where('status', 'completed')->sum('total_amount')
        ];

        // Kita gunakan view yang sama dengan seller tapi datanya global
        $pdf = Pdf::loadView('seller.reports.pdf', $data);
        return $pdf->download('Laporan_Global_Admin_'.date('Ymd').'.pdf');
    }
}