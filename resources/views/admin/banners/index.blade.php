<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Manajemen Banner
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Promosi Visual SquareOne</p>
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
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center justify-between border-l-4 border-blue-400">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-blue-400"></i>
                    <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- KOLOM KIRI: FORM UPLOAD & CROPPER (Sticky) --}}
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm sticky top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-6 w-1 bg-[#3C4142] rounded-full"></div>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest italic">Upload Baru</h3>
                    </div>
                    
                    <form id="form-banner-upload" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        {{-- 1. AREA INPUT FILE --}}
                        <div id="input-area">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Pilih Gambar</label>
                            <div class="relative border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:bg-slate-50 transition cursor-pointer group bg-slate-50/30">
                                <input type="file" id="image-input" name="image" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="text-slate-300 group-hover:text-slate-500 transition relative z-0">
                                    <i class="fas fa-cloud-upload-alt text-4xl mb-3"></i>
                                    <p class="text-[10px] font-black uppercase tracking-tighter">Pilih File Banner</p>
                                    <p class="text-[9px] mt-1 font-light italic">Maks 5MB. JPG/PNG.</p>
                                </div>
                            </div>
                        </div>

                        {{-- 2. AREA EDITOR CROPPER --}}
                        <div id="editor-area" class="hidden space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Sesuaikan Potongan</label>
                                <div class="img-container w-full aspect-video bg-slate-100 rounded-2xl overflow-hidden border-2 border-slate-100 shadow-inner">
                                    <img id="image-to-crop" src="" class="max-w-full">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Link Promo (Opsional)</label>
                                <input type="url" id="link-input" name="link" placeholder="https://..." 
                                       class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-800 focus:ring-slate-800 bg-slate-50/50">
                            </div>

                            <div class="flex gap-3">
                                <button type="button" id="btn-cancel" class="flex-1 bg-slate-100 text-slate-600 font-bold py-3.5 rounded-xl hover:bg-slate-200 transition text-[10px] uppercase tracking-widest">
                                    Batal
                                </button>
                                <button type="button" id="btn-crop-upload" class="flex-2 bg-[#3C4142] text-white font-bold py-3.5 px-4 rounded-xl hover:bg-black transition shadow-xl text-[10px] uppercase tracking-widest flex items-center justify-center gap-2">
                                    <span>✂️ Simpan</span>
                                </button>
                            </div>
                        </div>

                        {{-- Loading Indicator --}}
                        <div id="loading-indicator" class="hidden text-center p-6 bg-slate-50 rounded-2xl animate-pulse">
                            <i class="fas fa-circle-notch fa-spin text-2xl text-[#3C4142]"></i>
                            <p class="text-[10px] text-slate-600 font-black uppercase mt-3 tracking-widest">Memproses File...</p>
                        </div>
                    </form>
                </div>
            </div>

            {{-- KOLOM KANAN: LIST BANNER (8 Kolom) --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Banner Aktif</h3>
                    <div class="h-[1px] flex-1 bg-slate-100 mx-6"></div>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    @forelse($banners as $banner)
                        <div class="group bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 relative">
                            
                            {{-- Visual Image --}}
                            <div class="aspect-[3/1] bg-slate-50 overflow-hidden flex items-center justify-center p-1">
                                <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full object-contain rounded-[2rem]">
                            </div>

                            {{-- Info & Action --}}
                            <div class="p-6 flex justify-between items-center bg-white border-t border-slate-50">
                                <div class="truncate max-w-[75%]">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Target Navigasi:</p>
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full bg-blue-400"></div>
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $banner->link ?? 'Hanya Visual (Tanpa Link)' }}</p>
                                    </div>
                                </div>
                                
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Hapus banner ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center border border-red-100 shadow-sm">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-20 text-center">
                            <i class="fas fa-images text-5xl text-slate-100 mb-6 block"></i>
                            <p class="text-slate-300 text-[10px] uppercase tracking-[0.3em] italic">Gudang banner masih kosong</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        let cropper;
        const inputImage = document.getElementById('image-input');
        const imageToCrop = document.getElementById('image-to-crop');
        const inputArea = document.getElementById('input-area');
        const editorArea = document.getElementById('editor-area');
        const loadingIndicator = document.getElementById('loading-indicator');
        const btnCancel = document.getElementById('btn-cancel');
        const btnCropUpload = document.getElementById('btn-crop-upload');
        const linkInput = document.getElementById('link-input');

        inputImage.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function (event) {
                    imageToCrop.src = event.target.result;
                    inputArea.classList.add('hidden');
                    editorArea.classList.remove('hidden');
                    if (cropper) { cropper.destroy(); }
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: NaN, 
                        viewMode: 1, 
                        autoCropArea: 0.9,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        btnCancel.addEventListener('click', function() { resetEditor(); });

        btnCropUpload.addEventListener('click', function() {
            if (!cropper) return;
            editorArea.classList.add('hidden');
            loadingIndicator.classList.remove('hidden');

            cropper.getCroppedCanvas().toBlob((blob) => {
                const formData = new FormData();
                formData.append('image', blob, 'cropped-banner.jpg');
                formData.append('link', linkInput.value);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('{{ route("admin.banners.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData
                })
                .then(response => {
                    if (response.ok) { window.location.reload(); }
                    else { alert('Gagal simpan!'); resetEditor(); }
                })
                .catch(error => { resetEditor(); });
            }, 'image/jpeg', 0.9);
        });

        function resetEditor() {
            inputImage.value = '';
            linkInput.value = '';
            if (cropper) { cropper.destroy(); cropper = null; }
            imageToCrop.src = '';
            editorArea.classList.add('hidden');
            loadingIndicator.classList.add('hidden');
            inputArea.classList.remove('hidden');
        }
    </script>
</x-app-layout>