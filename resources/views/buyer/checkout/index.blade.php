<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    📦 Checkout Konfirmasi
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Finalisasi Pesanan SquareOne</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-8">
        
        {{-- Status Alerts --}}
        @if(session('error'))
            <div class="bg-red-600 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-white flex items-center gap-3">
                <span class="text-sm font-bold tracking-wide">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-green-400 flex items-center gap-3">
                <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- KOLOM KIRI: ALAMAT & BARANG --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- 1. Alamat Pengiriman --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <div class="flex justify-between items-center border-b border-slate-50 pb-6">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest italic">📍 Destinasi Pengiriman</h3>
                        <a href="{{ route('profile.edit') }}" class="text-[10px] font-black text-[#3C4142] uppercase tracking-widest border-b border-[#3C4142]">Ubah Profil</a>
                    </div>
                    
                    <div class="bg-slate-50/50 p-6 rounded-3xl border border-slate-50">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Wilayah / Kota</p>
                        <p class="text-sm font-black text-slate-800 uppercase tracking-tight">
                            {{ auth()->user()->city }} 
                            @if(isset($buyerCityData)) <span class="text-slate-400 font-bold italic ml-1">({{ $buyerCityData->island }})</span> @endif
                        </p>
                        
                        <div class="h-[1px] bg-slate-100 my-4"></div>
                        
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Detail Alamat</p>
                        <p class="text-xs font-bold text-slate-600 leading-relaxed italic">"{{ auth()->user()->address }}"</p>
                        
                        <p class="text-xs font-mono font-bold text-slate-800 mt-4 px-3 py-1.5 bg-white rounded-lg inline-block border border-slate-100 uppercase tracking-tighter">
                            📞 {{ auth()->user()->phone }}
                        </p>
                    </div>
                </div>

                {{-- 2. Rincian Barang --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-50">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest italic">🛍️ Rincian Item (Toko: {{ $seller->name }})</h3>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        @forelse($cartItems as $item)
                            <div class="flex gap-6 items-center border-b border-slate-50 pb-6 last:border-0 last:pb-0 group">
                                {{-- Thumbnail Produk --}}
                                <div class="w-20 h-20 bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 p-1 flex-shrink-0">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-contain rounded-xl group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="flex items-center justify-center h-full text-[8px] font-black text-slate-300 uppercase italic">No Image</div>
                                    @endif
                                </div>

                                {{-- Informasi Produk --}}
                                <div class="flex-1 space-y-1">
                                    <h4 class="font-black text-slate-800 text-sm tracking-tight capitalize">{{ $item->product->name }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Rp {{ number_format($item->product->price, 0, ',', '.') }} / pcs</p>

                                    {{-- ✅ LOGIC UPDATE QUANTITY DIPERBAIKI --}}
                                    {{-- Kita cek apakah item ini punya ID dari database Carts --}}
                                    @if(isset($item->id) && $item->id > 0)
                                        <form action="{{ route('buyer.cart.update', $item->id) }}" method="POST" class="flex items-center gap-3 mt-3">
                                            @csrf @method('PATCH')
                                            <span class="text-[9px] font-black text-slate-400 uppercase">Jumlah:</span>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                                min="1" max="{{ $item->product->stock }}" 
                                                class="w-14 bg-slate-50 text-center text-xs font-black border-none rounded-lg focus:ring-1 focus:ring-[#3C4142] p-1"
                                                onchange="this.form.submit()">
                                            <span class="text-[9px] font-bold text-slate-300 italic uppercase">Stok: {{ $item->product->stock }}</span>
                                        </form>
                                    @else
                                        {{-- Khusus mode "Beli Sekarang", input dikunci biar gak 404/Error --}}
                                        <div class="flex items-center gap-3 mt-3">
                                            <span class="text-[9px] font-black text-slate-400 uppercase">Jumlah:</span>
                                            <div class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-[#3C4142] italic">
                                                {{ $item->quantity }}
                                            </div>
                                            <span class="text-[9px] font-bold text-amber-500 italic uppercase">⚡ Beli Kilat</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Subtotal & Hapus --}}
                                <div class="text-right flex flex-col justify-between items-end h-20">
                                    <div class="font-black text-slate-900 text-sm italic tracking-tighter">
                                        Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                    </div>
                                    
                                    @if(isset($item->id) && $item->id > 0)
                                        <form action="{{ route('buyer.cart.destroy', $item->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[9px] font-black text-red-400 hover:text-red-600 uppercase tracking-widest transition" onclick="return confirm('Hapus barang?')">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <p class="text-[10px] font-black text-slate-300 uppercase italic">Keranjang Kosong</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PAYMENT SUMMARY --}}
            <div class="space-y-8 lg:sticky lg:top-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest italic border-b border-slate-50 pb-4">💳 Ringkasan Tagihan</h3>
                    
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between font-bold text-slate-400 uppercase">
                            <span>Subtotal Item</span>
                            <span class="font-black text-slate-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-slate-400 uppercase italic">
                            <span>Logistik ({{ $shippingType ?? 'Reguler' }})</span>
                            <span class="font-black text-slate-800">+Rp {{ number_format($shippingCost ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="h-[1px] bg-slate-50"></div>

                    <div class="flex justify-between items-center text-lg font-black text-[#3C4142] italic tracking-tighter">
                        <span>Grand Total</span>
                        <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>

                    {{-- QRIS Preview --}}
                    <div class="bg-slate-50/80 p-6 rounded-3xl border border-slate-100 text-center relative group cursor-pointer" onclick="openQRModal()">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">Metode Bayar: QRIS</p>
                        @if($seller->vendor_payment_info)
                            <img src="{{ asset('storage/' . $seller->vendor_payment_info) }}" class="w-32 h-32 mx-auto object-contain rounded-xl shadow-inner group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-32 h-32 mx-auto flex items-center justify-center bg-slate-100 rounded-xl">
                                <span class="text-[8px] font-black text-slate-300 uppercase">No QRIS</span>
                            </div>
                        @endif
                        <p class="text-[9px] text-slate-400 mt-4 leading-tight italic uppercase font-medium">Klik untuk memperbesar</p>
                    </div>

                    {{-- FORM PROCESS --}}
                    <form action="{{ route('buyer.checkout.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="seller_id" value="{{ $seller->id }}">
                        @foreach($cartItems as $item)
                            <input type="hidden" name="cart_ids[]" value="{{ $item->id ?? '' }}">
                        @endforeach

                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Bukti Bayar <span class="text-red-500">*</span>
                            </label>
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 border-dashed">
                                <input type="file" name="payment_proof" accept="image/*" required 
                                    class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:bg-[#3C4142] file:text-white hover:file:bg-black cursor-pointer">
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" required class="mt-1 rounded border-slate-200 text-[#3C4142]">
                                <span class="text-[10px] text-slate-700 font-black leading-relaxed uppercase tracking-tighter">
                                    Saya memastikan data alamat & kota sudah sesuai untuk pengiriman.
                                </span>
                            </label>
                        </div>

                        <button type="submit" class="w-full bg-[#3C4142] text-white font-black py-4 rounded-2xl shadow-2xl hover:bg-black transition transform hover:-translate-y-1 uppercase text-[10px] tracking-[0.2em]">
                            ✅ Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL QRIS --}}
    <div id="qrModal" class="hidden fixed inset-0 bg-slate-900/90 backdrop-blur-sm z-[99] flex items-center justify-center p-6" onclick="closeQRModal()">
        <div class="relative max-w-lg w-full bg-white rounded-[3rem] p-10 shadow-2xl text-center" onclick="event.stopPropagation()">
            <button onclick="closeQRModal()" class="absolute top-6 right-8 text-slate-300 hover:text-slate-800 transition text-2xl font-bold">&times;</button>
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter mb-1 italic">Scan QRIS Pembayaran</h3>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mb-8">Penerima: {{ $seller->name }}</p>
            <div class="bg-slate-50 p-10 rounded-[2.5rem] inline-block border border-slate-100">
                @if($seller->vendor_payment_info)
                    <img src="{{ asset('storage/' . $seller->vendor_payment_info) }}" class="max-w-full max-h-80 mx-auto object-contain">
                @endif
            </div>
            <div class="mt-10 p-6 bg-slate-50 rounded-2xl">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 text-center">Total Tagihan</p>
                <p class="text-3xl font-black text-[#3C4142] italic tracking-tighter text-center">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <script>
        function openQRModal() { document.getElementById('qrModal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
        function closeQRModal() { document.getElementById('qrModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeQRModal(); });
    </script>
</x-app-layout>