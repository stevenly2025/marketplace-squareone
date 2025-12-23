<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight italic">
            🛒 Keranjang Belanja Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FORM CHECKOUT: Mengirim ID Cart yang dicentang ke halaman Checkout --}}
            <form action="{{ route('buyer.checkout.index') }}" method="POST" id="form-cart">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    {{-- KOLOM KIRI: DAFTAR BARANG --}}
                    <div class="lg:col-span-2 space-y-6">
                        @forelse($cartItems->groupBy('product.seller_id') as $sellerId => $items)
                            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                                {{-- HEADER TOKO --}}
                                <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                                    <input type="checkbox" class="select-all-shop w-5 h-5 rounded border-slate-300 text-slate-800 focus:ring-slate-800">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-store text-slate-400"></i>
                                        <span class="font-black text-slate-800 uppercase text-xs tracking-wider">
                                            {{ $items->first()->product->seller->name }}
                                        </span>
                                    </div>
                                </div>

                                {{-- LIST PRODUK DALAM TOKO --}}
                                <div class="divide-y divide-slate-100">
                                    @foreach($items as $item)
                                        <div class="p-6 flex items-center gap-4 hover:bg-slate-50 transition">
                                            {{-- CHECKBOX ITEM --}}
                                            <input type="checkbox" name="cart_ids[]" value="{{ $item->id }}" 
                                                   class="item-checkbox w-5 h-5 rounded border-slate-300 text-slate-800 focus:ring-slate-800"
                                                   data-price="{{ $item->product->price }}" 
                                                   data-qty="{{ $item->quantity }}">

                                            {{-- GAMBAR PRODUK (DENGAN PENGAMAN) --}}
                                            <div class="w-20 h-20 flex-shrink-0 bg-slate-100 rounded-2xl overflow-hidden border border-slate-200">
                                                @if($item->product->image) {{-- DIPERBAIKI: 'image' bukan 'images' --}}
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                        <i class="fas fa-image text-2xl"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- INFO PRODUK --}}
                                            <div class="flex-1">
                                                <h4 class="font-bold text-slate-800 leading-tight mb-1">{{ $item->product->name }}</h4>
                                                <p class="text-xs text-slate-400 mb-2">Sisa stok: {{ $item->product->stock }}</p>
                                                <div class="flex items-center justify-between">
                                                    <span class="font-black text-slate-900">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                                                    
                                                    {{-- Update Qty & Hapus --}}
                                                    <div class="flex items-center gap-4">
                                                        <span class="text-sm font-bold text-slate-500">x{{ $item->quantity }}</span>
                                                        <button type="button" onclick="deleteItem({{ $item->id }})" class="text-red-400 hover:text-red-600 transition">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-slate-200">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-shopping-basket text-slate-300 text-3xl"></i>
                                </div>
                                <h3 class="text-slate-800 font-bold text-lg">Keranjangmu masih kosong</h3>
                                <p class="text-slate-400 text-sm mb-6">Ayo cari barang impianmu sekarang!</p>
                                <a href="{{ route('home') }}" class="inline-block bg-slate-900 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:bg-black transition">
                                    Mulai Belanja
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- KOLOM KANAN: RINGKASAN BELANJA (STICKY) --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 sticky top-24">
                            <h3 class="font-black text-slate-800 uppercase text-sm mb-6 border-b border-slate-50 pb-4">Ringkasan Belanja</h3>
                            
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-sm text-slate-500">
                                    <span>Total Barang Terpilih</span>
                                    <span id="total-items" class="font-bold text-slate-800">0</span>
                                </div>
                                <div class="flex justify-between items-center border-t border-slate-50 pt-4">
                                    <span class="text-base font-bold text-slate-800">Total Harga</span>
                                    <span id="total-price" class="text-xl font-black text-slate-900">Rp 0</span>
                                </div>
                            </div>

                            <button type="submit" id="btn-checkout" disabled
                                    class="w-full bg-slate-200 text-slate-400 py-4 rounded-2xl font-black uppercase tracking-widest transition shadow-lg cursor-not-allowed">
                                Lanjut Checkout
                            </button>

                            <p class="text-[10px] text-slate-400 mt-4 text-center">
                                <i class="fas fa-shield-alt mr-1"></i> Transaksi di SquareOne 100% Aman & Terpercaya
                            </p>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- Script JavaScript untuk hitungan otomatis (Live Subtotal) --}}
    <script>
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const shopCheckboxes = document.querySelectorAll('.select-all-shop');
        const totalPriceEl = document.getElementById('total-price');
        const totalItemsEl = document.getElementById('total-items');
        const btnCheckout = document.getElementById('btn-checkout');

        function calculate() {
            let total = 0;
            let count = 0;

            itemCheckboxes.forEach(cb => {
                if (cb.checked) {
                    const price = parseInt(cb.dataset.price);
                    const qty = parseInt(cb.dataset.qty);
                    total += price * qty;
                    count++;
                }
            });

            totalPriceEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
            totalItemsEl.innerText = count;

            if (count > 0) {
                btnCheckout.disabled = false;
                btnCheckout.classList.remove('bg-slate-200', 'text-slate-400', 'cursor-not-allowed');
                btnCheckout.classList.add('bg-slate-900', 'text-white', 'hover:bg-black');
            } else {
                btnCheckout.disabled = true;
                btnCheckout.classList.add('bg-slate-200', 'text-slate-400', 'cursor-not-allowed');
                btnCheckout.classList.remove('bg-slate-900', 'text-white', 'hover:bg-black');
            }
        }

        // Logic Centang per Toko
        shopCheckboxes.forEach(shopCb => {
            shopCb.addEventListener('change', function() {
                const container = this.closest('.bg-white');
                const items = container.querySelectorAll('.item-checkbox');
                items.forEach(item => {
                    item.checked = this.checked;
                });
                calculate();
            });
        });

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', calculate);
        });

        // Fungsi Hapus Barang (Panggil AJAX)
        function deleteItem(id) {
            if(confirm('Hapus barang ini dari keranjang?')) {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch(`/buyer/cart/destroy/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if(response.ok) window.location.reload();
                });
            }
        }
    </script>
</x-app-layout>