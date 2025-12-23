<x-app-layout>
    <x-slot name="header">
        <div class="max-w-4xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Pesanan #{{ $order->order_number }}
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Status dan Rincian Transaksi</p>
            </div>
            <a href="{{ route('buyer.orders') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 transition">
                 Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 px-4 md:px-0 space-y-8">

        {{-- Alerts --}}
        @if(session('success'))
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-green-400 flex items-center gap-3">
                <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-600 text-white px-6 py-4 rounded-2xl shadow-lg border-l-4 border-white flex items-center gap-3">
                <span class="text-sm font-bold tracking-wide">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Status Pesanan --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-6 border-b border-slate-50">
                <div class="space-y-2">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Progress Saat Ini</h3>
                    @php
                        $statusStyles = [
                            'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                            'processing' => 'bg-blue-50 text-blue-600 border-blue-100',
                            'shipped' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                            'completed' => 'bg-green-50 text-green-600 border-green-100',
                            'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                            'payment_rejected' => 'bg-red-50 text-red-600 border-red-100',
                        ];
                        $statusTexts = [
                            'pending' => '⏳ Menunggu Konfirmasi',
                            'processing' => '📦 Sedang Dikemas',
                            'shipped' => '🚚 Sedang Dikirim',
                            'completed' => '✅ Pesanan Selesai',
                            'cancelled' => '🚫 Pesanan Dibatalkan',
                            'payment_rejected' => '🚫 Pembayaran Bermasalah',
                        ];
                    @endphp
                    <div class="{{ $statusStyles[$order->status] ?? 'bg-slate-50' }} border px-5 py-2 rounded-xl text-sm font-black uppercase tracking-tight shadow-sm inline-block italic">
                        {{ $statusTexts[$order->status] ?? $order->status }}
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Dipesan Pada</p>
                    <p class="font-bold text-slate-800 text-sm">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
            </div>

            @if(in_array($order->status, ['cancelled', 'payment_rejected']) && $order->cancellation_reason)
                <div class="p-6 bg-red-50 rounded-3xl border border-red-100">
                    <p class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-1">Informasi Pembatalan</p>
                    <p class="text-sm font-black text-red-700 italic">"{{ $order->cancellation_reason }}"</p>
                    @if($order->cancellation_note)
                        <p class="text-[11px] text-red-600/70 mt-2 italic font-medium">Catatan: {{ $order->cancellation_note }}</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Section Pengiriman & Resi --}}
        @if($order->status == 'shipped')
            <div class="bg-[#3C4142] p-8 rounded-[2.5rem] shadow-2xl shadow-slate-200 text-white relative overflow-hidden">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div class="space-y-4 flex-1">
                        <h4 class="text-sm font-black uppercase tracking-[0.2em] italic text-slate-400">📦 Lacak Paket</h4>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold text-slate-500 uppercase">Nomor Resi Kurir</p>
                            <div class="flex items-center gap-3">
                                <p class="text-2xl font-black tracking-tighter italic font-mono">{{ $order->tracking_number ?? 'RESI-DIPROSES' }}</p>
                                @if($order->tracking_number)
                                    <button onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}'); alert('Resi disalin!')" 
                                            class="bg-white/10 hover:bg-white/20 text-white px-3 py-1 rounded-lg text-[9px] font-black uppercase transition border border-white/10">
                                        Salin
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-center md:text-right w-full md:w-auto">
                        <p class="text-[9px] text-slate-400 mb-3 font-black uppercase tracking-widest">Konfirmasi Penerimaan</p>
                        <form action="{{ route('buyer.orders.complete', $order->id) }}" method="POST" onsubmit="return confirm('Pastikan barang sudah diterima dengan baik.');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full md:w-auto bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition shadow-lg transform hover:-translate-y-1">
                                ✅ Pesanan Diterima
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        {{-- Kendala & Upload Ulang --}}
        @if(in_array($order->status, ['cancelled', 'payment_rejected']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center justify-between gap-4">
                    <div>
                        <h4 class="font-black text-slate-800 text-[10px] uppercase tracking-widest mb-1">Hubungi Penjual</h4>
                        <p class="text-[11px] text-slate-400 font-medium">Bicarakan solusi terbaik Anda.</p>
                    </div>
                    <a href="https://wa.me/{{ $order->seller->phone ?? '6280000000' }}?text=Halo, saya mau tanya soal pesanan {{ $order->order_number }}..." 
                       target="_blank" class="bg-green-500 text-white p-4 rounded-2xl hover:bg-green-600 transition shadow-lg">
                        💬
                    </a>
                </div>

                @if($order->status == 'payment_rejected')
                    <div class="bg-blue-50 p-8 rounded-[2.5rem] border border-blue-100 shadow-sm">
                        <h3 class="font-black text-[10px] text-blue-900 uppercase tracking-widest mb-4">Re-upload Bukti Bayar</h3>
                        <form action="{{ route('buyer.orders.reupload', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="file" name="payment_proof" required class="block w-full text-[10px] text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-white file:text-blue-700 shadow-sm">
                            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition">
                                Kirim Bukti Baru
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif

        {{-- Rincian Produk & Alamat --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Alamat Pengiriman</h3>
                <div class="text-sm text-slate-600 pl-6 border-l-2 border-[#3C4142]">
                    <p class="font-black text-slate-900 uppercase tracking-tight mb-1">{{ $order->buyer->name }}</p>
                    <p class="font-medium italic leading-relaxed">"{{ $order->shipping_address }}"</p>
                    <p class="mt-3 font-mono text-xs font-bold bg-white inline-block px-3 py-1 rounded-lg border border-slate-100">📞 {{ $order->shipping_phone }}</p>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mb-2">Item Terbeli</h3>
                <div class="space-y-6">
                    @foreach($order->items as $item)
                        <div class="flex gap-6 items-center border-b border-slate-50 pb-6 last:border-0 last:pb-0">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl overflow-hidden border border-slate-100 p-1 flex-shrink-0">
                                @if($item->product_image)
                                    <img src="{{ asset('storage/' . $item->product_image) }}" class="w-full h-full object-contain rounded-xl">
                                @else
                                    <div class="text-[8px] font-black text-slate-300 uppercase flex items-center justify-center h-full">No Img</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-black text-slate-800 text-sm tracking-tight truncate capitalize">{{ $item->product_name }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right font-black text-slate-900 text-sm italic">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 pt-8 border-t border-slate-100 space-y-3">
                    @php
                        $subtotalItems = $order->items->sum(fn($i) => $i->price * $i->quantity);
                        $shippingFee = $order->total_amount - $subtotalItems;
                    @endphp
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <span>Subtotal Barang</span>
                        <span class="text-slate-600">Rp {{ number_format($subtotalItems, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">
                        <span>Ongkos Kirim</span>
                        <span class="text-slate-600">Rp {{ number_format($shippingFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-4 text-2xl font-black text-[#3C4142] border-t border-slate-50 mt-4 tracking-tighter italic">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Batalkan Pesanan - ✅ DIPERBAIKI --}}
        @php
            $deadline = $order->created_at->addHours(24);
            $minutesLeft = now()->diffInMinutes($deadline, false);
            $hoursLeft = floor($minutesLeft / 60);
            $isPending = $order->status == 'pending';
        @endphp

        @if($isPending && $minutesLeft > 0)
            <div x-data="{ openCancel: false }" class="space-y-4">
                <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-6 rounded-[2rem] border border-red-50 shadow-sm gap-6">
                    <div class="flex items-center gap-4">
                        <div class="text-2xl">⏳</div>
                        <div>
                            <p class="text-xs font-black text-slate-800 uppercase tracking-tight">Kebijakan Batal Instan</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Sisa waktu garansi: <span class="text-red-500 italic">{{ $hoursLeft }} Jam lagi</span></p>
                        </div>
                    </div>
                    <button @click="openCancel = !openCancel" class="w-full sm:w-auto text-[10px] font-black bg-red-50 text-red-500 border border-red-100 px-8 py-3 rounded-2xl hover:bg-red-500 hover:text-white transition uppercase tracking-widest">
                        Batalkan Pesanan
                    </button>
                </div>

                {{-- ✅ FIXED: Form action dan field name --}}
                <div x-show="openCancel" x-cloak x-transition class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-red-100">
                    <form action="{{ route('buyer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Yakin batalkan pesanan?');" class="space-y-6">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alasan Pembatalan</label>
                                <select name="cancellation_reason" required class="w-full text-xs font-black uppercase tracking-tighter rounded-xl border-slate-100 bg-slate-50 p-3 focus:ring-[#3C4142]">
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="Berubah Pikiran">Berubah Pikiran</option>
                                    <option value="Salah Pilih Produk">Salah Pilih Produk</option>
                                    <option value="Salah Alamat">Salah Alamat</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Catatan (Opsional)</label>
                                <textarea name="cancellation_note" rows="2" class="w-full text-xs font-medium rounded-xl border-slate-100 bg-slate-50 p-3 focus:ring-[#3C4142]" placeholder="Tulis alasan tambahan jika perlu..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-red-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-100">
                                Konfirmasi Batalkan Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>