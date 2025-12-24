<x-app-layout>
    <x-slot name="header">
        <div class="max-w-4xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                     Tambah Produk Baru
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Lengkapi Detail Etalase Toko Anda</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-12 px-4 md:px-0">
        <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 space-y-8">
                {{-- Judul Seksi --}}
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-6 w-1 bg-[#3C4142] rounded-full"></div>
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest italic">Informasi Utama Produk</h3>
                </div>

                {{-- Nama Produk --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Produk</label>
                    <input type="text" name="name" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 text-sm font-bold text-slate-700 focus:border-slate-800 focus:ring-slate-800 placeholder:text-slate-300" placeholder="Contoh: Kemeja Flanel SquareOne" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kategori --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kategori</label>
                        <select name="category_id" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 text-sm font-bold text-slate-700 focus:border-slate-800 focus:ring-slate-800" required>
                            <option value="" class="font-normal">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Stok --}}
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Persediaan (Stok)</label>
                        <input type="number" name="stock" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 text-sm font-bold text-slate-700 focus:border-slate-800 focus:ring-slate-800" placeholder="0" required>
                    </div>
                </div>

                {{-- Harga --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Harga Jual (Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold text-xs uppercase">Rp</div>
                        <input type="number" name="price" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 pl-12 p-4 text-sm font-black text-slate-800 focus:border-slate-800 focus:ring-slate-800" placeholder="0" required>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi Produk</label>
                    <textarea name="description" rows="5" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 text-sm font-medium text-slate-600 focus:border-slate-800 focus:ring-slate-800 placeholder:text-slate-300" placeholder="Ceritakan detail keunggulan produk Anda..." required></textarea>
                </div>

                {{-- Foto Produk --}}
                <div class="space-y-2 pt-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Foto Produk</label>
                    <div class="border-2 border-dashed border-slate-100 p-8 rounded-3xl bg-slate-50/30">
                        {{-- DIPERBAIKI: name="image" bukan "images[]" --}}
                        <input type="file" name="image" class="block w-full text-[10px] text-slate-500
                            file:mr-6 file:py-2.5 file:px-6
                            file:rounded-full file:border-0
                            file:text-[10px] file:font-black
                            file:bg-[#3C4142] file:text-white
                            hover:file:bg-black file:cursor-pointer file:transition-all" required>
                        <div class="mt-4 flex items-center gap-2 text-[9px] text-slate-400 italic font-medium leading-relaxed">
                            <i class="fas fa-info-circle"></i>
                            {{-- DIPERBAIKI: Update pesan maksimal ukuran --}}
                            <span>Pilih satu gambar produk. Format: JPG, PNG, WEBP. Maks 5MB.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-6 pt-4">
                <button type="submit" class="flex-1 bg-[#3C4142] hover:bg-black text-white font-black py-4 rounded-2xl shadow-2xl shadow-slate-200 transition-all transform hover:-translate-y-1 uppercase text-[10px] tracking-[0.2em]">
                     Publikasikan Produk
                </button>
                <a href="{{ route('seller.products.index') }}" class="px-8 py-4 bg-white border border-slate-200 text-slate-400 font-black rounded-2xl hover:text-slate-800 hover:border-slate-800 transition-all uppercase text-[10px] tracking-widest">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-app-layout>