<x-app-layout>
    <x-slot name="header">
        <div class="max-w-4xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Perbarui Kategori
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">ID Kategori: #{{ $category->id }}</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-12 px-4 md:px-0">
        <div class="bg-white overflow-hidden shadow-sm rounded-[2.5rem] border border-slate-100 p-10">
            
            <div class="flex items-center gap-3 mb-10">
                <div class="h-8 w-1.5 bg-[#3C4142] rounded-full"></div>
                <h3 class="font-black text-slate-800 uppercase text-xs tracking-[0.2em] italic">
                    Detail Perubahan: {{ $category->name }}
                </h3>
            </div>

            {{-- FORM EDIT --}}
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                {{-- 1. Input Nama --}}
                <div class="max-w-xl">
                    <label for="name" class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Nama Kategori</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                        class="w-full rounded-2xl border-slate-200 focus:border-slate-800 focus:ring-slate-800 text-sm font-bold text-slate-700 p-4 bg-slate-50/50">
                    @error('name')
                        <p class="text-red-500 text-[10px] mt-2 font-black italic">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. Input Gambar (Live Preview) --}}
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Visual Ikon</label>
                    
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-8 p-8 border-2 border-dashed border-slate-100 rounded-[2rem] bg-slate-50/30">
                        
                        {{-- PREVIEW GAMBAR --}}
                        <div class="relative group">
                            <p class="absolute -top-6 left-0 text-[9px] text-slate-400 font-bold uppercase">Pratinjau</p>
                            <div class="w-32 h-32 bg-white rounded-3xl shadow-xl shadow-slate-200/50 flex items-center justify-center p-2 border border-slate-50 transition-transform group-hover:scale-105 duration-300">
                                <img id="preview-img" 
                                     src="{{ $category->image ? asset('storage/' . $category->image) : 'https://via.placeholder.com/150?text=No+Image' }}" 
                                     class="max-w-full max-h-full object-contain">
                            </div>
                        </div>
                        
                        {{-- INPUT FILE --}}
                        <div class="flex-1 space-y-4">
                            <div>
                                <h4 class="text-sm font-black text-slate-800 italic">Ganti Gambar</h4>
                                <p class="text-[10px] text-slate-400 leading-relaxed">Pilih file baru jika ingin mengubah ikon. <br>Format yang didukung: JPG, PNG. Maksimal 2MB.</p>
                            </div>

                            <input type="file" name="image" id="category_image" accept="image/*"
                                   onchange="previewImage(this)"
                                   class="block w-full text-[10px] text-slate-500
                                   file:mr-5 file:py-2.5 file:px-6
                                   file:rounded-full file:border-0
                                   file:text-[10px] file:font-black
                                   file:bg-[#3C4142] file:text-white
                                   hover:file:bg-black file:cursor-pointer file:transition-colors">
                        </div>
                    </div>
                    @error('image')
                        <p class="text-red-500 text-[10px] mt-2 font-black italic">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center gap-6 mt-12 pt-8 border-t border-slate-50">
                    <button type="submit" class="bg-[#3C4142] text-white font-black py-4 px-10 rounded-2xl shadow-2xl shadow-slate-300 hover:bg-black transition-all transform hover:-translate-y-1 uppercase text-[10px] tracking-widest">
                        💾 Simpan Perubahan
                    </button>
                    
                    <a href="{{ route('admin.categories.index') }}" class="text-slate-400 font-bold text-[10px] uppercase tracking-widest hover:text-slate-800 transition-colors">
                        Batal & Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- SCRIPT LIVE PREVIEW --}}
    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview-img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>