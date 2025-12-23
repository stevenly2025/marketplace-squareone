<x-app-layout>
    <x-slot name="header">
        <div class="max-w-4xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Laporan Penjualan
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Rekapitulasi Performa Toko</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-12 px-4 md:px-0 space-y-8">
        {{-- Card Form Filter --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-6 w-1 bg-[#3C4142] rounded-full"></div>
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest italic">Konfigurasi Periode</h3>
                </div>

                <p class="text-slate-500 text-sm mb-10 leading-relaxed">
                    Silakan tentukan rentang tanggal. Hanya pesanan dengan status <span class="font-black text-green-600 italic">SELESAI</span> yang akan direkap.
                </p>

                <form action="{{ route('seller.reports.index') }}" method="GET" class="space-y-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Mulai Dari</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" required
                                class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 text-sm font-bold text-slate-700 focus:border-slate-800 focus:ring-slate-800 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Sampai Dengan</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" required
                                class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 text-sm font-bold text-slate-700 focus:border-slate-800 focus:ring-slate-800 transition-all">
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50 flex flex-col md:flex-row justify-end gap-4">
                        {{-- Tombol Preview --}}
                        <button type="submit" class="bg-slate-100 text-slate-600 font-black py-4 px-10 rounded-2xl hover:bg-slate-200 transition-all uppercase text-[10px] tracking-[0.2em]">
                            Preview Data
                        </button>

                        {{-- Tombol Download PDF (Mengarah ke route export) --}}
                        <button type="submit" formaction="{{ route('seller.reports.export') }}" class="bg-[#3C4142] text-white font-black py-4 px-10 rounded-2xl shadow-2xl shadow-slate-200 hover:bg-black transition-all transform hover:-translate-y-1 uppercase text-[10px] tracking-[0.2em]">
                            Unduh Laporan PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Section Tabel Preview (Hanya tampil jika ada filter) --}}
        @if(isset($orders))
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden animate-fade-in">
                <div class="px-10 py-6 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Hasil Preview: {{ $orders->count() }} Transaksi</h3>
                    <span class="text-[11px] font-black text-slate-800 italic">Total: Rp {{ number_format($orders->sum('total_amount'), 0, ',', '.') }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                <th class="px-10 py-5">No. Order</th>
                                <th class="px-10 py-5">Tanggal Selesai</th>
                                <th class="px-10 py-5">Pembeli</th>
                                <th class="px-10 py-5 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($orders as $order)
                                <tr class="text-sm hover:bg-slate-50/50 transition-colors">
                                    <td class="px-10 py-5 font-black text-slate-800">#{{ $order->order_number }}</td>
                                    <td class="px-10 py-5 text-slate-500">{{ $order->updated_at->format('d/m/Y') }}</td>
                                    <td class="px-10 py-5 font-bold text-slate-700">{{ $order->buyer->name }}</td>
                                    <td class="px-10 py-5 text-right font-black text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-10 py-16 text-center">
                                        <p class="text-slate-300 text-[10px] uppercase tracking-[0.2em] font-black italic">Data tidak ditemukan untuk periode ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="mt-8 text-center">
            <p class="text-[9px] text-slate-300 font-bold uppercase tracking-[0.3em]">SquareOne Automated Reporting System</p>
        </div>
    </div>
</x-app-layout>