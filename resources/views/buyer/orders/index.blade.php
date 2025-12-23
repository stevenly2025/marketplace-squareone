<x-app-layout>
    <x-slot name="header">
        <div class="max-w-5xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Pesanan Saya
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Pantau status belanja Anda</p>
            </div>
            <a href="{{ route('home') }}" class="bg-[#3C4142] text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition shadow-xl">
                Belanja Lagi
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 py-10 pb-20 space-y-6">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-green-400 flex items-center gap-3">
                <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        @forelse($orders as $order)
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden group transition-all hover:shadow-md">
                
                {{-- Header Kartu Order --}}
                <div class="px-8 py-4 bg-slate-50/50 border-b border-slate-50 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">#{{ $order->order_number }}</span>
                        <div class="h-3 w-[1px] bg-slate-200"></div>
                        <span class="text-[10px] font-black text-[#3C4142] uppercase italic">🏢 {{ $order->seller->name }}</span>
                    </div>
                    
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'processing' => 'bg-blue-50 text-blue-600 border-blue-100',
                            'shipped' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                            'completed' => 'bg-green-50 text-green-600 border-green-100',
                            'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                            'payment_rejected' => 'bg-red-50 text-red-600 border-red-100',
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu Konfirmasi',
                            'processing' => 'Dikemas',
                            'shipped' => 'Dikirim',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            'payment_rejected' => 'Bukti Ditolak',
                        ];
                    @endphp
                    <span class="{{ $statusColors[$order->status] ?? 'bg-slate-50 text-slate-500' }} border px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-widest italic shadow-sm">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>

                {{-- Detail Produk Utama --}}
                <div class="p-8 flex items-center space-x-6">
                    <div class="w-20 h-20 bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 flex-shrink-0 p-1">
                        <img src="{{ asset('storage/' . $order->items->first()->product_image) }}" class="w-full h-full object-contain rounded-xl">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-base font-black text-slate-800 tracking-tight truncate capitalize">{{ $order->items->first()->product_name }}</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mt-1 tracking-wider">
                            {{ $order->items->count() }} Produk • Total: 
                            <span class="text-sm font-black text-[#3C4142] italic ml-1">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </p>
                    </div>
                </div>

                <div class="px-8 pb-8 space-y-6">
                    {{-- Navigasi Aksi --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <a href="{{ route('buyer.orders.show', $order->id) }}" 
                           class="bg-slate-50 text-slate-400 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-[#3C4142] hover:text-white transition text-center shadow-sm">
                            Rincian Pesanan
                        </a>

                        @if(in_array($order->status, ['completed', 'cancelled', 'payment_rejected']))
                            <form action="{{ route('buyer.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat pesanan?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full bg-red-50 text-red-400 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-red-500 hover:text-white transition shadow-sm border border-red-50">
                                    Hapus Histori
                                </button>
                            </form>
                        @endif

                        @if($order->status === 'shipped')
                            <form action="{{ route('buyer.orders.complete', $order->id) }}" method="POST" class="md:col-span-2">
                                @csrf @method('PATCH')
                                <button class="w-full bg-blue-600 text-white px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-blue-100 hover:bg-blue-700 transition">
                                    Konfirmasi Pesanan Diterima
                                </button>
                            </form>
                        @endif

                        @if($order->status === 'cancelled')
                            @php
                                $waText = "Halo {$order->seller->name}, saya ingin meminta refund untuk Order #{$order->order_number} karena telah dibatalkan di SquareOne.";
                                $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $order->seller->phone) . "?text=" . urlencode($waText);
                            @endphp
                            <a href="{{ $waLink }}" target="_blank" 
                               class="md:col-span-2 bg-green-500 text-white px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-green-100 hover:bg-green-600 transition text-center italic">
                                Hubungi Penjual (Refund)
                            </a>
                        @endif
                    </div>

                    {{-- Section Review --}}
                    @if($order->status === 'completed')
                        @php 
                            $userReview = \App\Models\Review::where('order_id', $order->id)->where('user_id', auth()->id())->first();
                        @endphp
                        
                        @if(!$userReview)
                            <div class="bg-slate-50/50 p-6 rounded-[2rem] border border-dashed border-slate-200">
                                <h5 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-4 italic italic">Bagikan Pengalaman Anda</h5>
                                <form action="{{ route('buyer.review.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                    <input type="hidden" name="product_id" value="{{ $order->items->first()->product_id }}">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <select name="rating" class="w-full rounded-xl border-slate-100 bg-white text-[10px] font-black uppercase tracking-tighter p-3 focus:ring-[#3C4142]">
                                            <option value="5">⭐⭐⭐⭐⭐ Sangat Puas</option>
                                            <option value="4">⭐⭐⭐⭐ Puas</option>
                                            <option value="3">⭐⭐⭐ Cukup</option>
                                            <option value="2">⭐⭐ Kurang</option>
                                            <option value="1">⭐ Tidak Puas</option>
                                        </select>

                                        <div class="relative bg-white border border-slate-100 rounded-xl overflow-hidden p-2 flex items-center">
                                            <input type="file" name="image" accept="image/*"
                                                onchange="previewImage(this, 'preview-{{ $order->id }}')"
                                                class="block w-full text-[9px] text-slate-400 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:bg-slate-100 file:text-slate-500 hover:file:bg-[#3C4142] hover:file:text-white cursor-pointer"/>
                                        </div>
                                    </div>

                                    <div id="preview-box-{{ $order->id }}" class="hidden">
                                        <div class="relative inline-block">
                                            <img id="preview-{{ $order->id }}" src="" class="h-24 w-24 rounded-2xl border-4 border-white shadow-sm object-cover">
                                            <div class="absolute -top-2 -right-2 bg-green-500 text-white rounded-full p-1 shadow-lg">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <input type="text" name="comment" placeholder="Tulis ulasan singkat..." class="flex-1 text-[11px] font-bold rounded-xl border-slate-100 bg-white p-3 focus:ring-[#3C4142]" required>
                                        <button type="submit" class="bg-[#3C4142] text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-black transition shadow-lg">
                                            Kirim
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-100 flex items-start justify-between gap-4">
                                <div class="space-y-2 flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Ulasan Anda</span>
                                        <div class="flex text-[8px] text-amber-400">
                                            @for($i = 1; $i <= 5; $i++) {{ $i <= $userReview->rating ? '★' : '☆' }} @endfor
                                        </div>
                                    </div>
                                    <p class="text-xs font-bold text-slate-600 leading-relaxed">"{{ $userReview->comment }}"</p>
                                </div>
                                @if($userReview->image)
                                    <a href="{{ asset('storage/' . $userReview->image) }}" target="_blank" class="flex-shrink-0 relative group">
                                        <img src="{{ asset('storage/' . $userReview->image) }}" class="h-14 w-14 rounded-xl object-cover border border-white shadow-sm group-hover:opacity-75 transition">
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                            <i class="fas fa-search-plus text-white text-[10px]"></i>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[2.5rem] border border-slate-100 p-20 text-center">
                <div class="flex flex-col items-center gap-3">
                    <i class="fas fa-shopping-basket text-slate-100 text-5xl mb-2"></i>
                    <p class="text-slate-300 text-[10px] uppercase tracking-[0.3em] font-black italic">Belum Ada Riwayat Belanja</p>
                </div>
            </div>
        @endforelse

        <div class="mt-8 px-4">
            {{ $orders->links() }}
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const previewBox = document.getElementById('preview-box-' + previewId.replace('preview-', ''));
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewBox.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = "";
                previewBox.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>