<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Markas Seller SquareOne
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Ringkasan Aktivitas Toko Anda</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-12">
        
        {{-- 1. KARTU STATISTIK (Minimalis Charcoal) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Omzet Toko --}}
            <div class="bg-[#3C4142] p-8 rounded-[2rem] text-white shadow-2xl shadow-slate-200">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Pendapatan</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xs font-bold text-slate-400">Rp</span>
                    <span class="text-2xl font-black tracking-tighter">{{ number_format($stats['total_sales'], 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Pesanan Menunggu --}}
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pesanan Baru</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-800 tracking-tighter">{{ $stats['pending_orders'] }}</span>
                    <span class="text-[10px] text-red-500 font-black uppercase italic">Perlu Tindakan</span>
                </div>
            </div>

            {{-- Produk Aktif --}}
            <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Etalase Produk</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-800 tracking-tighter">{{ $stats['active_products'] }}</span>
                    <span class="text-[10px] text-green-500 font-black uppercase italic">Aktif Live</span>
                </div>
            </div>
        </div>

        {{-- 2. QUICK ACTIONS (Hanya Menu yang Ada) --}}
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('seller.orders') }}" class="flex-1 min-w-[200px] p-6 bg-white border border-slate-200 rounded-[1.5rem] flex items-center justify-between hover:border-slate-800 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#3C4142] group-hover:text-white transition text-slate-400">
                        <i class="fas fa-shopping-basket text-sm"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-700">Manajemen Pesanan</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>

            <a href="{{ route('home') }}" class="flex-1 min-w-[200px] p-6 bg-white border border-slate-200 rounded-[1.5rem] flex items-center justify-between hover:border-slate-800 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center group-hover:bg-[#3C4142] group-hover:text-white transition text-slate-400">
                        <i class="fas fa-eye text-sm"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-700">Lihat Tampilan Toko</span>
                </div>
                <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            </a>
        </div>

        {{-- 3. PESANAN YANG PERLU TINDAKAN --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Konfirmasi Pengiriman Tercepat</h3>
                <div class="h-[1px] flex-1 bg-slate-100 mx-6"></div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                                <th class="px-8 py-5">Identitas Pembeli</th>
                                <th class="px-8 py-5">Total Pembayaran</th>
                                <th class="px-8 py-5 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($need_action as $order)
                            <tr class="group hover:bg-slate-50/80 transition-colors duration-200">
                                <td class="px-8 py-5">
                                    <div class="font-black text-slate-800 text-sm tracking-tight">{{ $order->buyer->name }}</div>
                                    <div class="text-[9px] text-slate-400 uppercase font-black tracking-tighter mt-1">{{ $order->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-black text-slate-900 text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <a href="{{ route('seller.orders') }}" class="inline-block bg-[#3C4142] text-white text-[9px] font-black uppercase tracking-widest px-6 py-2.5 rounded-xl hover:bg-black transition-all shadow-lg shadow-slate-100">
                                        Konfirmasi Pesanan
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check-double text-slate-200"></i>
                                        </div>
                                        <p class="text-slate-300 text-[10px] uppercase tracking-[0.2em] italic font-medium">Semua pesanan sudah ditangani.</p>
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