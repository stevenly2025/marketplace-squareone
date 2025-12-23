<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Produk Saya
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Kelola Stok & Etalase Toko</p>
            </div>
            <a href="{{ route('seller.products.create') }}" class="bg-[#3C4142] hover:bg-black text-white font-black py-2.5 px-6 rounded-xl shadow-xl shadow-slate-200 transition-all duration-300 text-[10px] uppercase tracking-widest flex items-center gap-2">
                <span>+</span> Tambah Produk
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-6">
        
        {{-- Flash Message --}}
        @if(session('success'))
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-green-400 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-400"></i>
                <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        {{-- BAR PENCARIAN (MINIMALIS CHARCOAL) --}}
        <div class="bg-white p-4 rounded-[2rem] border border-slate-100 shadow-sm">
            <form action="{{ route('seller.products.index') }}" method="GET" class="flex gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Cari nama produk Anda di sini..." 
                        class="w-full rounded-2xl border-slate-100 bg-slate-50/50 p-4 text-xs font-bold text-slate-700 focus:border-slate-800 focus:ring-slate-800 placeholder:text-slate-300 transition-all">
                </div>
                <button type="submit" class="bg-[#3C4142] text-white px-8 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all shadow-md">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('seller.products.index') }}" class="bg-slate-100 text-slate-400 px-6 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center hover:bg-slate-200 transition-all">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- TABEL PRODUK --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5">Visual</th>
                            <th class="px-8 py-5">Nama & Kategori</th>
                            <th class="px-8 py-5">Harga Jual</th>
                            <th class="px-8 py-5 text-center">Persediaan</th>
                            <th class="px-8 py-5 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @forelse($products as $product)
                        <tr class="group hover:bg-slate-50/80 transition-colors duration-200">
                            {{-- Gambar Produk --}}
                            <td class="px-8 py-5">
                                <div class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 p-1 flex items-center justify-center">
                                    @if($product->image) {{-- DIPERBAIKI: 'image' bukan 'images' --}}
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-contain rounded-xl">
                                    @else
                                        <div class="text-[8px] font-black text-slate-300 uppercase italic">No Img</div>
                                    @endif
                                </div>
                            </td>

                            {{-- Nama & Kategori --}}
                            <td class="px-8 py-5">
                                <div class="font-black text-slate-800 text-sm tracking-tight capitalize">{{ $product->name }}</div>
                                <div class="mt-1">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">
                                        {{ $product->category->name ?? 'Tanpa Kategori' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Harga --}}
                            <td class="px-8 py-5">
                                <p class="text-sm font-black text-slate-900 italic">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </td>

                            {{-- Stok --}}
                            <td class="px-8 py-5 text-center">
                                @if($product->stock > 5)
                                    <span class="text-xs font-black text-slate-800 tracking-tighter">{{ $product->stock }}</span>
                                @else
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-black text-red-500 tracking-tighter">{{ $product->stock }}</span>
                                        <span class="text-[8px] font-black text-red-400 uppercase tracking-tighter mt-0.5">Stok Tipis!</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-8 py-5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('seller.products.edit', $product->id) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-600 hover:bg-[#3C4142] hover:text-white transition-all shadow-sm border border-slate-100">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    <form action="{{ route('seller.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm border border-red-100">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center">
                                        <i class="fas fa-box-open text-slate-200"></i>
                                    </div>
                                    <p class="text-slate-300 text-[10px] uppercase tracking-[0.2em] italic font-medium">
                                        {{ request('search') ? 'Produk tidak ditemukan.' : 'Etalase toko Anda masih kosong.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="px-4 mt-6">
            {{ $products->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</x-app-layout>