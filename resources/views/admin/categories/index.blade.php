<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Kelola Kategori
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Organisir Produk SquareOne</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-10">
        
        {{-- Pesan Berhasil --}}
        @if(session('success'))
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center justify-between border-l-4 border-blue-400 animate-bounce">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-blue-400"></i>
                    <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- KOLOM KIRI: FORM TAMBAH (Minimalis Charcoal) --}}
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm sticky top-24">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-6 italic border-b pb-2 inline-block border-slate-800">Tambah Baru</h3>
                    
                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Nama Kategori</label>
                            <input type="text" name="name" id="name" placeholder="Contoh: Elektronik" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-800 focus:ring-slate-800 placeholder:text-slate-300">
                            @error('name')
                                <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Ikon / Gambar</label>
                            <input type="file" name="image" accept="image/*" 
                                class="block w-full text-[11px] text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-[10px] file:font-black
                                file:bg-slate-100 file:text-slate-700
                                hover:file:bg-slate-200 cursor-pointer border border-dashed border-slate-200 p-2 rounded-xl">
                            <p class="text-[9px] text-slate-400 mt-2 font-light italic">*Format: JPG, PNG. Maksimal 2MB.</p>
                        </div>

                        <button type="submit" class="w-full bg-[#3C4142] text-white font-bold py-3.5 rounded-xl hover:bg-black transition-all duration-300 shadow-xl shadow-slate-200 uppercase text-[10px] tracking-[0.2em]">
                            + Simpan Kategori
                        </button>
                    </form>
                </div>
            </div>

            {{-- KOLOM KANAN: DAFTAR KATEGORI (Sleek Table) --}}
            <div class="lg:col-span-8 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Kategori Aktif</h3>
                    <div class="h-[1px] flex-1 bg-slate-100 mx-6"></div>
                </div>

                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                                    <th class="px-8 py-5">Ikon</th>
                                    <th class="px-8 py-5">Info Kategori</th>
                                    <th class="px-8 py-5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($categories as $category)
                                <tr class="group hover:bg-slate-50/80 transition-colors">
                                    {{-- Kolom Gambar --}}
                                    <td class="px-8 py-5">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center overflow-hidden">
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}" class="w-full h-full object-contain p-1">
                                            @else
                                                <span class="text-xl">📦</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Kolom Info --}}
                                    <td class="px-8 py-5">
                                        <div class="font-black text-slate-800 text-sm tracking-tight">{{ $category->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono italic">/{{ $category->slug }}</div>
                                    </td>
                                    
                                    {{-- Kolom Aksi --}}
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                               class="w-9 h-9 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>

                                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fas fa-folder-open text-3xl text-slate-100"></i>
                                            <p class="text-slate-300 text-[10px] uppercase tracking-widest italic">Belum ada data kategori</p>
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
    </div>
</x-app-layout>