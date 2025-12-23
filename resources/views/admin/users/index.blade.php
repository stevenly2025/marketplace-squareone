<x-app-layout>
    <x-slot name="header">
        <div class="max-w-6xl mx-auto flex justify-between items-center px-4 md:px-0">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight italic">
                    Manajemen Pengguna
                </h2>
                <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] mt-1">Kontrol Akses SquareOne</p>
            </div>
            <div class="text-right">
                <p class="text-[11px] font-black text-slate-800 border-b-2 border-slate-800 pb-1 uppercase tracking-wider">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto py-10 px-4 md:px-0 space-y-8">
        
        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3 border-l-4 border-green-400">
                <i class="fas fa-check-circle text-green-400"></i>
                <span class="text-sm font-bold tracking-wide">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-600 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-3 border-l-4 border-white">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="text-sm font-bold tracking-wide">{{ session('error') }}</span>
            </div>
        @endif

        {{-- CONTAINER TABEL UTAMA --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-8 py-6 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] italic">Daftar Akun Terdaftar</h3>
                <span class="text-[10px] font-bold text-slate-400 bg-white border border-slate-200 px-3 py-1 rounded-full italic">Total: {{ $users->total() }} User</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap border-collapse">
                    <thead>
                        <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-white border-b border-slate-50">
                            <th class="px-8 py-5">Identitas Pengguna</th>
                            <th class="px-8 py-5">Kontak & Email</th>
                            <th class="px-8 py-5 text-center">Tipe Akun</th>
                            <th class="px-8 py-5 text-center">Status Keamanan</th>
                            <th class="px-8 py-5 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 bg-white">
                        @foreach($users as $user)
                        <tr class="group hover:bg-slate-50/80 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-[#3C4142] text-white flex items-center justify-center font-black text-xs shadow-md">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-slate-800 text-sm tracking-tight">{{ $user->name }}</div>
                                        <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Gabung: {{ $user->created_at->translatedFormat('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm">
                                <div class="font-bold text-slate-600">{{ $user->email }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $user->phone ?? 'No Phone' }}</div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @php
                                    $roles = [
                                        'superadmin' => 'bg-purple-50 text-purple-600 border-purple-100',
                                        'seller' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'buyer' => 'bg-slate-50 text-slate-600 border-slate-100'
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-lg border {{ $roles[$user->role] ?? 'bg-gray-50' }} text-[9px] font-black uppercase tracking-widest">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($user->is_banned)
                                    <span class="px-3 py-1 rounded-lg bg-red-50 text-red-600 border border-red-100 text-[9px] font-black uppercase tracking-widest italic">⛔ Terblokir</span>
                                @else
                                    <span class="px-3 py-1 rounded-lg bg-green-50 text-green-600 border border-green-100 text-[9px] font-black uppercase tracking-widest italic">✅ Aktif</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($user->role !== 'superadmin')
                                    <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full max-w-[120px] mx-auto py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition shadow-md hover:scale-105 active:scale-95
                                            {{ $user->is_banned ? 'bg-green-500 text-white hover:bg-green-600 shadow-green-100' : 'bg-[#3C4142] text-white hover:bg-black shadow-slate-200' }}">
                                            {{ $user->is_banned ? 'Buka Akses' : 'Blokir User' }}
                                        </button>
                                    </form>
                                @else
                                    <div class="text-[9px] font-black text-slate-300 uppercase italic tracking-widest">Otoritas Penuh</div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>