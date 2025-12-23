<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Detail Pesanan #{{ $order->order_number }}
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Sistem Manajemen Order SquareOne</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-8">
        
        <a href="{{ route('seller.orders') }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Pesanan
        </a>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-green-400 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-400"></i>
                <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="bg-red-600 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-white">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-sm font-bold">{{ session('error') ?? 'Terjadi kesalahan input.' }}</span>
                </div>
                @if($errors->any())
                    <ul class="mt-2 text-[10px] list-disc list-inside opacity-80">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KOLOM KIRI: INFO PESANAN & BARANG (2 Kolom) --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- 1. Informasi Pesanan & Alamat --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">
                    <div class="flex justify-between items-center border-b border-slate-50 pb-6">
                        <div>
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest italic">Informasi Pesanan</h3>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase">{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'processing' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'shipped' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                'completed' => 'bg-green-50 text-green-600 border-green-100',
                                'payment_rejected' => 'bg-orange-50 text-orange-600 border-orange-100',
                                'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                            ];
                        @endphp
                        <span class="{{ $statusColors[$order->status] ?? 'bg-slate-50' }} border px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest italic">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-slate-50/50 p-6 rounded-3xl border border-slate-50">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Pembeli</p>
                            <p class="text-sm font-black text-slate-800">{{ $order->buyer->name }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $order->buyer->email }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">No. WhatsApp</p>
                            <p class="text-sm font-black text-slate-800 font-mono">{{ $order->shipping_phone ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Alamat Pengiriman</p>
                            <p class="text-xs font-bold text-slate-600 leading-relaxed italic">"{{ $order->shipping_address ?? '-' }}"</p>
                        </div>
                    </div>

                    {{-- Alasan Pembatalan (Jika Ada) --}}
                    @if(in_array($order->status, ['cancelled', 'payment_rejected']) && $order->cancellation_reason)
                        <div class="p-6 bg-red-50 rounded-2xl border border-red-100">
                            <p class="text-[9px] font-black text-red-500 uppercase tracking-widest mb-1">Alasan Penolakan/Pembatalan</p>
                            <p class="text-sm font-black text-red-700 italic">{{ $order->cancellation_reason }}</p>
                            @if($order->cancellation_note)
                                <p class="text-xs text-red-600 mt-2 opacity-80 font-medium">Catatan: {{ $order->cancellation_note }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 2. Rincian Barang --}}
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden font-sans">
                    <div class="px-8 py-5 bg-slate-50/50 border-b border-slate-50">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest italic">Daftar Produk</h3>
                    </div>
                    <div class="p-8">
                        <table class="w-full text-left">
                            <thead class="text-[9px] font-black text-slate-300 uppercase tracking-widest border-b border-slate-50">
                                <tr>
                                    <th class="pb-4">Produk</th>
                                    <th class="pb-4 text-center">Qty</th>
                                    <th class="pb-4 text-right">Harga Satuan</th>
                                    <th class="pb-4 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($order->items as $item)
                                <tr class="text-sm">
                                    <td class="py-5 font-bold text-slate-700">{{ $item->product_name }}</td>
                                    <td class="py-5 text-center font-bold text-slate-400">{{ $item->quantity }}</td>
                                    <td class="py-5 text-right font-medium text-slate-500">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="py-5 text-right font-black text-slate-800 italic">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="pt-8 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Belanja</td>
                                    <td class="pt-8 text-right text-xl font-black text-[#3C4142] italic tracking-tighter">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- 3. Rincian Pendapatan (Lengkap Sesuai Kode Lo) --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <h3 class="text-[10px] font-black text-slate-800 uppercase tracking-widest mb-4 italic flex items-center gap-2">
                        <i class="fas fa-wallet text-slate-400"></i> Analisis Pendapatan Toko
                    </h3>
                    
                    @php
                        $subtotalItems = $order->items->sum(fn($i) => $i->price * $i->quantity);
                        $shippingFee = $order->total_amount - $subtotalItems;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-5 bg-green-50/50 rounded-2xl border border-green-100">
                            <p class="text-[9px] font-black text-green-600 uppercase tracking-widest mb-1">Pendapatan Bersih</p>
                            <p class="text-lg font-black text-green-700 italic">Rp {{ number_format($subtotalItems, 0, ',', '.') }}</p>
                            <p class="text-[9px] text-green-500 font-medium italic mt-1">*Dana masuk ke saldo toko Anda</p>
                        </div>
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 border-dashed">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Dana Logistik (Ongkir)</p>
                            <p class="text-lg font-black text-slate-500 italic">Rp {{ number_format($shippingFee, 0, ',', '.') }}</p>
                            <p class="text-[9px] text-slate-400 font-medium italic mt-1">*Diserahkan ke pihak kurir</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: BUKTI BAYAR & STATUS (1 Kolom) --}}
            <div class="space-y-8">
                
                {{-- Bukti Pembayaran (Versi Mini & Zoom) --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic">Bukti Transfer</h3>
                    @if($order->payment_proof)
                        <div class="relative group cursor-zoom-in">
                            <div class="w-full h-48 bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 p-2 shadow-inner">
                                <img src="{{ asset('storage/' . $order->payment_proof) }}" class="w-full h-full object-contain">
                            </div>
                            <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank" 
                               class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300 rounded-2xl">
                                <span class="bg-white text-[#3C4142] px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-xl">
                                    <i class="fas fa-search-plus"></i> Lihat Full
                                </span>
                            </a>
                        </div>
                    @else
                        <div class="h-40 bg-slate-50 rounded-2xl flex flex-col items-center justify-center border border-dashed border-slate-200">
                            <i class="fas fa-receipt text-slate-200 text-3xl mb-2"></i>
                            <p class="text-[9px] font-bold text-slate-300 uppercase italic text-center px-4">Pembeli Belum <br> Unggah Bukti</p>
                        </div>
                    @endif
                </div>

                {{-- Kontrol Status (Update Pengiriman) --}}
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 italic">Panel Eksekusi</h3>
                    
                    @if($order->status == 'pending')
                        <div class="space-y-4">
                            <form action="{{ route('seller.orders.update', $order->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" name="status" value="processing" class="w-full bg-[#3C4142] text-white font-black py-4 rounded-2xl hover:bg-black transition-all shadow-xl shadow-slate-200 uppercase text-[10px] tracking-widest">
                                    ✅ Terima Pesanan
                                </button>
                            </form>
                            <button onclick="document.getElementById('rejectForm').classList.toggle('hidden')" class="w-full text-red-500 font-black text-[9px] uppercase tracking-widest py-2">
                                ❌ Tolak Pembayaran
                            </button>
                            
                            <div id="rejectForm" class="hidden pt-6 space-y-4 border-t border-slate-50">
                                <form action="{{ route('seller.orders.update', $order->id) }}" method="POST" class="space-y-4">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="payment_rejected">
                                    <div>
                                        <label class="text-[9px] font-black text-slate-400 uppercase mb-2 block tracking-widest">Alasan Penolakan</label>
                                        <select name="cancellation_reason" class="w-full rounded-xl border-slate-100 text-[10px] font-bold uppercase bg-slate-50 p-3">
                                            <option value="Bukti Pembayaran Tidak Valid">Bukti Tidak Valid</option>
                                            <option value="Nominal Transfer Salah">Nominal Salah</option>
                                            <option value="Stok Habis">Stok Habis</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <textarea name="cancellation_note" rows="2" placeholder="Catatan opsional..." class="w-full rounded-xl border-slate-100 text-xs p-3 bg-slate-50"></textarea>
                                    <button type="submit" class="w-full bg-red-600 text-white font-black py-3 rounded-xl uppercase text-[9px] tracking-widest shadow-lg">Konfirmasi Tolak</button>
                                </form>
                            </div>
                        </div>

                    @elseif($order->status == 'processing')
                        <form action="{{ route('seller.orders.update', $order->id) }}" method="POST" class="space-y-6">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="shipped">
                            <div>
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Nomor Resi Pengiriman</label>
                                <input type="text" name="tracking_number" required placeholder="Contoh: JNE1234567..." 
                                    class="w-full rounded-2xl border-slate-100 bg-slate-50 text-sm font-black text-slate-700 p-4 focus:ring-[#3C4142]">
                            </div>
                            <button type="submit" class="w-full bg-[#3C4142] text-white font-black py-4 rounded-2xl uppercase text-[10px] tracking-widest shadow-xl shadow-slate-200">
                                🚚 Update Resi & Kirim
                            </button>
                        </form>
                        
                        {{-- Opsi Batal --}}
                        <button onclick="document.getElementById('cancelForm').classList.toggle('hidden')" class="w-full text-slate-400 font-bold text-[9px] uppercase tracking-widest py-4">Batalkan Pesanan</button>
                        <div id="cancelForm" class="hidden pt-4 space-y-4 border-t border-slate-50">
                            <form action="{{ route('seller.orders.update', $order->id) }}" method="POST" class="space-y-4">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <select name="cancellation_reason" class="w-full rounded-xl border-slate-100 text-[10px] font-bold uppercase bg-slate-50 p-3">
                                    <option value="Stok Habis">Stok Habis</option>
                                    <option value="Permintaan Pembeli">Permintaan Pembeli</option>
                                </select>
                                <button type="submit" class="w-full bg-red-600 text-white font-black py-3 rounded-xl uppercase text-[9px]">Konfirmasi Batal</button>
                            </form>
                        </div>

                    @elseif($order->status == 'shipped' || $order->status == 'completed')
                        <div class="p-6 bg-slate-50 rounded-[2rem] border border-slate-100 text-center">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nomor Resi</p>
                            <p class="text-lg font-black text-slate-800 font-mono tracking-tighter">{{ $order->tracking_number }}</p>
                            <div class="mt-4 px-4 py-2 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase inline-block">
                                {{ $order->status == 'shipped' ? '📦 Dalam Perjalanan' : '✅ Selesai' }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] italic">Pesanan Berhenti</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>