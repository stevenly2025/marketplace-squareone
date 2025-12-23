@php
    $avgRating = $product->reviews_avg_rating ?? 0;
    $totalReviews = $product->reviews_count ?? 0;
    $totalSold = $product->order_items_sum_quantity ?? 0; 
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    🛍️ Detail Produk
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Koleksi Eksklusif SquareOne</p>
            </div>
            <a href="/" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto space-y-8">
            
            {{-- SEKSI 1: INFO PRODUK & PEMBELIAN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[3rem] border border-slate-100 grid grid-cols-1 lg:grid-cols-2 gap-12 p-10">
                {{-- Foto --}}
                <div class="relative group bg-slate-50 rounded-[2.5rem] h-[500px] overflow-hidden border border-slate-50 flex items-center justify-center p-8">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="object-contain h-full w-full group-hover:scale-110 transition duration-700">
                    @else
                        <div class="text-slate-200 font-black italic">NO IMAGE</div>
                    @endif
                    <span class="absolute top-6 left-6 bg-[#3C4142] text-white px-5 py-1.5 rounded-full text-[9px] font-black uppercase tracking-[0.2em] shadow-xl italic">
                        {{ $product->category?->name ?? 'General' }}
                    </span>
                </div>

                {{-- Konten Kanan --}}
                <div class="flex flex-col justify-between">
                    <div class="space-y-6">
                        <h1 class="text-4xl font-black text-slate-800 tracking-tighter capitalize">{{ $product->name }}</h1>
                        
                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-black text-[#3C4142] italic">{{ number_format((float)$avgRating, 1) }}</span>
                                <div class="text-yellow-400 text-[10px] flex">
                                    @for($i=1; $i<=5; $i++) <i class="{{ $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i> @endfor
                                </div>
                            </div>
                            <div class="h-4 w-px bg-slate-200"></div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $totalReviews }} Ulasan</div>
                            <div class="h-4 w-px bg-slate-200"></div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $totalSold }} Terjual</div>
                        </div>

                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 italic">
                            <span class="text-4xl font-black text-slate-900 tracking-tighter">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>

                        {{-- Toko --}}
                        <div class="flex items-center gap-4 p-5 border border-slate-100 rounded-[2rem]">
                            <div class="bg-[#3C4142] text-white w-12 h-12 rounded-full flex items-center justify-center font-black">
                                {{ substr($product->seller->name ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase">{{ $product->seller->name }}</h4>
                                <p class="text-[10px] text-slate-400 font-medium italic"><i class="fas fa-map-marker-alt"></i> {{ $product->seller->city }}</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Deskripsi</h3>
                            <p class="text-sm text-slate-500 leading-relaxed">{{ $product->description }}</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-10 pt-8 border-t border-slate-100">
                        @auth
                            @if(auth()->user()->role === 'buyer')
                                <div class="flex flex-col md:flex-row items-center gap-6">
                                    <div class="flex items-center bg-slate-50 p-2 rounded-2xl border border-slate-100">
                                        <button onclick="updateQty(-1)" class="w-10 h-10 font-black text-slate-400">&minus;</button>
                                        <input type="number" id="qty-display" value="1" min="1" max="{{ $product->stock }}" onchange="syncQty(this.value)" class="w-14 bg-transparent border-none text-center font-black text-slate-800 focus:ring-0">
                                        <button onclick="updateQty(1)" class="w-10 h-10 font-black text-slate-400">&plus;</button>
                                    </div>

                                    <div class="flex gap-4 flex-1 w-full">
                                        <form action="{{ route('buyer.cart.add', $product->id) }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" id="qty-input-cart" value="1">
                                            <button type="submit" class="w-full py-4 rounded-2xl border-2 border-[#3C4142] text-[#3C4142] font-black uppercase text-[10px] tracking-widest hover:bg-slate-50 transition shadow-sm">
                                                + Keranjang
                                            </button>
                                        </form>

                                        <form action="{{ route('buyer.checkout.direct') }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" id="qty-input-direct" value="1">
                                            <button type="submit" class="w-full py-4 rounded-2xl bg-[#3C4142] text-white font-black uppercase text-[10px] tracking-widest hover:bg-black transition shadow-2xl">
                                                ⚡ Beli Sekarang
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <p class="mt-4 text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Stok: {{ $product->stock }} unit</p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full py-5 bg-[#3C4142] text-white rounded-2xl font-black uppercase text-[10px] tracking-widest text-center block shadow-2xl">Login untuk Belanja</a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- SEKSI 2: ULASAN DENGAN FILTER BATANG --}}
            <div class="bg-white shadow-sm sm:rounded-[3rem] border border-slate-100 p-10">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-[0.3em] italic mb-10">Collector Feedback</h3>
                
                <div class="flex flex-col md:flex-row gap-12 mb-12 border-b border-slate-50 pb-12">
                    {{-- Angka Utama --}}
                    <div class="flex flex-col items-center justify-center min-w-[150px] bg-slate-50 rounded-[2rem] p-6">
                        <h3 class="text-6xl font-black text-slate-800 italic">{{ number_format($avgRating, 1) }}</h3>
                        <div class="text-yellow-400 text-sm my-2 flex">
                            @for($i=1; $i<=5; $i++) <i class="{{ $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i> @endfor
                        </div>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ $totalReviews }} Ulasan</p>
                    </div>

                    {{-- FILTER BATANG --}}
                    <div class="flex-1 space-y-3">
                        @for($i = 5; $i >= 1; $i--)
                            @php 
                                $count = $product->reviews->where('rating', $i)->count();
                                $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                            @endphp
                            <button onclick="filterRating({{ $i }})" class="flex items-center gap-4 w-full group hover:bg-slate-50 p-1 rounded-lg transition">
                                <div class="flex items-center gap-1 min-w-[30px]">
                                    <span class="text-xs font-black text-slate-600">{{ $i }}</span>
                                    <i class="fas fa-star text-[10px] text-yellow-400"></i>
                                </div>
                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#3C4142] group-hover:bg-black transition-all" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 w-8 text-right">{{ $count }}</span>
                            </button>
                        @endfor
                    </div>
                </div>

                {{-- DAFTAR REVIEW --}}
                <div id="reviews-list" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($product->reviews as $review)
                        <div class="review-card bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-md" data-rating="{{ $review->rating }}">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center font-black text-[#3C4142] text-xs">
                                        {{ substr($review->user->name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $review->user->name }}</h4>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">{{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex text-yellow-400 text-[8px]">
                                    @for($j = 1; $j <= 5; $j++) <i class="{{ $j <= $review->rating ? 'fas' : 'far' }} fa-star"></i> @endfor
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 font-medium italic leading-relaxed mb-6">"{{ $review->comment }}"</p>
                            @if($review->image)
                                <a href="{{ asset('storage/' . $review->image) }}" target="_blank" class="block w-32 h-32 rounded-2xl overflow-hidden border-4 border-white shadow-sm hover:scale-105 transition">
                                    <img src="{{ asset('storage/' . $review->image) }}" class="w-full h-full object-cover">
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-20 text-slate-300 italic font-black uppercase tracking-[0.3em] text-xs">No Reviews Yet</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // FUNGSI QUANTITY
        const maxStock = {{ $product->stock }};
        function updateQty(change) {
            let input = document.getElementById('qty-display');
            let newVal = parseInt(input.value) + change;
            if(newVal < 1) newVal = 1;
            if(newVal > maxStock) newVal = maxStock;
            input.value = newVal;
            syncQty(newVal);
        }
        function syncQty(val) {
            document.getElementById('qty-input-cart').value = val;
            document.getElementById('qty-input-direct').value = val;
        }

        // FUNGSI FILTER REVIEW
        function filterRating(rating) {
            const cards = document.querySelectorAll('.review-card');
            cards.forEach(card => {
                if (card.getAttribute('data-rating') == rating) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</x-app-layout>