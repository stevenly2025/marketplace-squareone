<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Dashboard Saya
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Selamat datang kembali di SquareOne</p>
            </div>
            <a href="{{ route('home') }}" class="bg-[#3C4142] text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl transform hover:-translate-y-1">
                Belanja Lagi
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-10">
        
        {{-- 1. KARTU STATISTIK (Minimalis Charcoal Style) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Pesanan --}}
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Frekuensi Belanja</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800 tracking-tighter">{{ $stats['total_orders'] }}</span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase italic">Pesanan</span>
                </div>
                <div class="absolute -bottom-2 -right-2 text-slate-50 text-6xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="bg-[#3C4142] p-8 rounded-[2rem] text-white shadow-2xl shadow-slate-200 relative overflow-hidden group">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Dana Terbelanjakan</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xs font-bold text-slate-400">Rp</span>
                    <span class="text-2xl font-black tracking-tighter">{{ number_format($stats['total_spent'], 0, ',', '.') }}</span>
                </div>
                <div class="absolute -bottom-2 -right-2 text-white/5 text-6xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            {{-- Sedang Proses --}}
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Status Aktif</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-slate-800 tracking-tighter">{{ $stats['pending'] }}</span>
                    <span class="text-[10px] text-orange-500 font-black uppercase italic">Sedang Berjalan</span>
                </div>
                <div class="absolute -bottom-2 -right-2 text-slate-50 text-6xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>

        {{-- 2. AKTIVITAS TERAKHIR --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Ringkasan Belanja Terakhir</h3>
                <div class="h-[1px] flex-1 bg-slate-100 mx-6"></div>
                <a href="{{ route('buyer.orders') }}" class="text-[9px] font-black text-[#3C4142] hover:text-black uppercase tracking-widest border-b border-[#3C4142]">Lihat Semua</a>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-50">
                                <th class="px-8 py-5">Informasi Produk</th>
                                <th class="px-8 py-5">Total Bayar</th>
                                <th class="px-8 py-5 text-center">Status Paket</th>
                                <th class="px-8 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 bg-white">
                            @forelse($recent_orders as $order)
                                <tr class="group hover:bg-slate-50/80 transition-colors duration-200">
                                    <td class="px-8 py-5">
                                        <div class="font-black text-slate-800 text-sm tracking-tight capitalize">{{ $order->items->first()->product_name }}</div>
                                        <div class="text-[9px] text-slate-400 font-bold uppercase mt-1">
                                            @if($order->items->count() > 1)
                                                +{{ $order->items->count() - 1 }} Item Lainnya • 
                                            @endif
                                            {{ $order->created_at->translatedFormat('d M Y') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 font-black text-slate-900 italic">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        @php
                                            $colors = [
                                                'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                                'processing' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'shipped' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                'completed' => 'bg-green-50 text-green-600 border-green-100',
                                                'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                                            ];
                                            $labels = [
                                                'pending' => 'Menunggu',
                                                'processing' => 'Diproses',
                                                'shipped' => 'Dikirim',
                                                'completed' => 'Selesai',
                                                'cancelled' => 'Batal',
                                            ];
                                        @endphp
                                        <span class="{{ $colors[$order->status] ?? 'bg-slate-50 text-slate-600' }} border px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-sm">
                                            {{ $labels[$order->status] ?? $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-block bg-slate-50 text-slate-400 hover:bg-[#3C4142] hover:text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-200">
                                                <i class="fas fa-shopping-basket"></i>
                                            </div>
                                            <p class="text-slate-300 text-[10px] uppercase tracking-[0.2em] italic font-medium">Belum ada riwayat aktivitas belanja.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>