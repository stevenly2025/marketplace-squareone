<x-app-layout>
    <x-slot name="header">
        {{-- Pembatasan lebar agar tidak terlalu lebar ke samping (Minimalis) --}}
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Panel Kontrol SuperAdmin
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Sistem Manajemen SquareOne</p>
            </div>
            <div class="text-right">
                {{-- TANGGAL REAL-TIME INDONESIA --}}
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-12">
        
        {{-- 1. BAGIAN STATISTIK (Kartu Minimalis Modern) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Omzet --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Omzet</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xs font-bold text-slate-400">Rp</span>
                    <span class="text-xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['total_sales'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Pesanan --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Total Pesanan</p>
                <p class="text-xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['total_orders'] ?? 0) }}</p>
            </div>

            {{-- Pengguna --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pengguna</p>
                <p class="text-xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['total_users'] ?? 0) }}</p>
            </div>

            {{-- Produk --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Produk Aktif</p>
                <p class="text-xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['total_products'] ?? 0) }}</p>
            </div>
        </div>

        {{-- 2. MENU NAVIGASI (Warna Charcoal SquareOne) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="{{ route('admin.banners.index') }}" class="group bg-[#3C4142] p-8 rounded-2xl text-white transition-all duration-300 hover:bg-black hover:-translate-y-1">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h4 class="text-lg font-bold italic mb-1">Banner Promosi</h4>
                        <p class="text-xs text-slate-400 font-light">Atur tampilan iklan halaman depan</p>
                    </div>
                    <div class="mt-8 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                        Kelola Sekarang <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="group bg-white p-8 rounded-2xl border border-slate-200 text-slate-800 transition-all duration-300 hover:border-slate-800 hover:-translate-y-1">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h4 class="text-lg font-bold italic mb-1">Master Kategori</h4>
                        <p class="text-xs text-slate-500 font-light">Kelola pengelompokan produk</p>
                    </div>
                    <div class="mt-8 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                        Kelola Sekarang <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="group bg-white p-8 rounded-2xl border border-slate-200 text-slate-800 transition-all duration-300 hover:border-red-500 hover:-translate-y-1">
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <h4 class="text-lg font-bold italic mb-1">Kelola Pengguna</h4>
                        <p class="text-xs text-slate-500 font-light">Atur akses dan status akun</p>
                    </div>
                    <div class="mt-8 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                        Kelola Sekarang <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- 3. TABEL AKTIVITAS TERBARU (Real-time Date Format) --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Transaksi Terbaru</h3>
                <div class="h-[1px] flex-1 bg-slate-100 mx-6"></div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                                <th class="px-8 py-4">ID Pesanan</th>
                                <th class="px-8 py-4">Nama Pembeli</th>
                                <th class="px-8 py-4">Total Bayar</th>
                                <th class="px-8 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recent_orders as $order)
                            <tr class="text-sm hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-4 font-bold text-slate-400 text-xs">#{{ $order->order_number }}</td>
                                <td class="px-8 py-4">
                                    <span class="font-bold text-slate-800">{{ $order->buyer->name }}</span>
                                    {{-- FORMAT TANGGAL INDONESIA --}}
                                    <span class="block text-[10px] text-slate-400 mt-0.5">
                                        {{ $order->created_at->translatedFormat('H:i') }} • {{ $order->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 font-black text-slate-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="px-8 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase {{ $order->status == 'completed' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-slate-300 italic text-xs tracking-widest">Tidak ada aktivitas transaksi ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>