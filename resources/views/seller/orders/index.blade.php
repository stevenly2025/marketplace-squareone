<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Daftar Pesanan Masuk
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Kelola Transaksi Penjualan</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-6">
        
        {{-- TABEL PESANAN --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5">Informasi Pesanan</th>
                            <th class="px-8 py-5">Data Pembeli</th>
                            <th class="px-8 py-5">Total Pembayaran</th>
                            <th class="px-8 py-5 text-center">Status Transaksi</th>
                            <th class="px-8 py-5 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @forelse($orders as $order)
                        <tr class="group hover:bg-slate-50/80 transition-colors duration-200">
                            {{-- Nomor Pesanan & Tanggal --}}
                            <td class="px-8 py-5">
                                <div class="font-black text-slate-800 text-sm tracking-tighter">#{{ $order->order_number }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase mt-1">
                                    {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                                </div>
                            </td>

                            {{-- Nama Pembeli --}}
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-black">
                                        {{ substr($order->buyer->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-700 tracking-tight">{{ $order->buyer->name }}</span>
                                </div>
                            </td>

                            {{-- Total Harga --}}
                            <td class="px-8 py-5">
                                <p class="text-sm font-black text-slate-900">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </p>
                            </td>

                            {{-- Status Pesanan --}}
                            <td class="px-8 py-5 text-center">
                                @php
                                    $colors = [
                                        'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                        'processing' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'shipped' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                        'completed' => 'bg-green-50 text-green-600 border-green-100',
                                        'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                                    ];
                                @endphp
                                <span class="{{ $colors[$order->status] ?? 'bg-slate-100' }} border px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-sm">
                                    {{ $order->status }}
                                </span>
                            </td>

                            {{-- Tombol Kelola --}}
                            <td class="px-8 py-5 text-center">
                                <a href="{{ route('seller.orders.show', $order->id) }}" class="inline-block bg-[#3C4142] text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-slate-100 hover:-translate-y-1 transform">
                                    Kelola <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center">
                                        <i class="fas fa-inbox text-slate-200"></i>
                                    </div>
                                    <p class="text-slate-300 text-[10px] uppercase tracking-[0.2em] italic font-medium">Belum ada pesanan yang masuk.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="px-4">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>