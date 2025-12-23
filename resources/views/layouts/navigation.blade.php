<nav x-data="{ open: false }" class="bg-[#3C4142] sticky top-0 z-50 shadow-2xl font-sans border-b border-white/5">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20"> <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="bg-white p-1.5 rounded-lg shadow-md transition group-hover:scale-105 duration-300">
                            <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto" alt="Logo" />
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-white group-hover:text-gray-200 transition italic">SquareOne</span>
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-6">
                @auth
                    {{-- Jalur Navigasi per Role --}}
                    <div class="flex items-center gap-6 mr-2 pr-6 border-r border-white/20">
                        @if(auth()->user()->role === 'superadmin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Dashboard</x-nav-link>
                            <x-nav-link :href="route('admin.banners.index')" :active="request()->routeIs('admin.banners.*')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Banner Promo</x-nav-link>
                            <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Kategori</x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Manajemen User</x-nav-link>
                        
                        @elseif(auth()->user()->role === 'seller')
                            <x-nav-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.dashboard')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Dashboard Toko</x-nav-link>
                            <x-nav-link :href="route('seller.orders')" :active="request()->routeIs('seller.orders*')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Pesanan Masuk</x-nav-link>
                            <x-nav-link :href="route('seller.products.index')" :active="request()->routeIs('seller.products.*')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Produk Saya</x-nav-link>
                            <x-nav-link :href="route('seller.reports.index')" :active="request()->routeIs('seller.reports.*')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Laporan Toko</x-nav-link>
                        
                        @elseif(auth()->user()->role === 'buyer')
                            <x-nav-link :href="route('buyer.dashboard')" :active="request()->routeIs('buyer.dashboard')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Akun Saya</x-nav-link>
                            <x-nav-link :href="route('buyer.orders')" :active="request()->routeIs('buyer.orders*')" class="text-sm font-semibold text-white/90 hover:text-white border-white/0 hover:border-white/100 transition">Riwayat Belanja</x-nav-link>
                        @endif
                        
                        @if(auth()->user()->role !== 'buyer')
                            <a href="{{ route('home') }}" class="text-sm font-medium text-white/40 hover:text-white transition italic">Lihat Toko</a>
                        @endif
                    </div>

                    {{-- Icon Keranjang Putih Premium --}}
                    @if(auth()->user()->role === 'buyer')
                    <div class="relative">
                        <a href="{{ route('buyer.cart.index') }}" class="group relative flex items-center justify-center w-11 h-11 rounded-xl bg-white/10 hover:bg-white/20 transition duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white group-hover:scale-110 transition transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            @php
                                $cartCountNav = \App\Models\Cart::where('buyer_id', auth()->id())->count();
                            @endphp
                            @if($cartCountNav > 0)
                                <span class="absolute -top-1 -right-1 flex h-5 w-5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-5 w-5 bg-yellow-400 text-[10px] text-black font-bold items-center justify-center border-2 border-[#3C4142]">
                                        {{ $cartCountNav }}
                                    </span>
                                </span>
                            @endif
                        </a>
                    </div>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border-none text-sm font-bold text-white hover:text-gray-200 transition duration-150 ease-in-out focus:outline-none">
                                <div class="flex flex-col items-end">
                                    <span class="text-sm tracking-tight capitalize">{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] text-white/40 font-light tracking-widest uppercase italic leading-none">{{ Auth::user()->role }}</span>
                                </div>
                                <svg class="ms-2 h-4 w-4 text-white/30" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')" class="text-gray-700 font-medium">Profil Saya</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-500 font-bold italic">Keluar</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-6">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-white/70 hover:text-white transition">Masuk</a>
                        <a href="{{ route('register') }}" class="bg-white text-[#3C4142] text-sm font-bold px-6 py-2.5 rounded-xl hover:bg-gray-100 transition shadow-lg">Daftar</a>
                    </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden gap-3">
                @auth
                    @if(auth()->user()->role === 'buyer')
                        <a href="{{ route('buyer.cart.index') }}" class="relative p-2 bg-white/10 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            @if($cartCountNav > 0)
                                <span class="absolute top-0 right-0 h-4 w-4 bg-yellow-400 text-[9px] text-black font-bold flex items-center justify-center rounded-full border border-[#3C4142]">{{ $cartCountNav }}</span>
                            @endif
                        </a>
                    @endif
                @endauth
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 focus:outline-none transition">
                    <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#2b2f30] border-t border-white/5 animate-fade-in-down">
        @auth
            <div class="pt-4 pb-3 space-y-2 px-4">
                @php $navClass = "block px-4 py-3 rounded-xl text-white hover:bg-white/5 font-medium transition"; @endphp
                
                <a href="{{ route('home') }}" class="{{ $navClass }} text-yellow-400 font-bold border border-yellow-400/20 mb-2">🏠 Halaman Depan</a>

                @if(auth()->user()->role === 'superadmin')
                    <a href="{{ route('admin.dashboard') }}" class="{{ $navClass }}">Dashboard Admin</a>
                    <a href="{{ route('admin.banners.index') }}" class="{{ $navClass }}">Banner Promo</a>
                    <a href="{{ route('admin.categories.index') }}" class="{{ $navClass }}">Kategori</a>
                @elseif(auth()->user()->role === 'seller')
                    <a href="{{ route('seller.dashboard') }}" class="{{ $navClass }}">Dashboard Toko</a>
                    <a href="{{ route('seller.orders') }}" class="{{ $navClass }}">Pesanan Masuk</a>
                    <a href="{{ route('seller.products.index') }}" class="{{ $navClass }}">Produk Saya</a>
                    <a href="{{ route('seller.reports.index') }}" class="{{ $navClass }}">Laporan Toko</a>
                @else
                    <a href="{{ route('buyer.dashboard') }}" class="{{ $navClass }}">Akun Saya</a>
                    <a href="{{ route('buyer.orders') }}" class="{{ $navClass }}">Riwayat Belanja</a>
                @endif
            </div>

            <div class="pt-4 pb-6 border-t border-white/5 px-4 bg-[#1e2122]">
                <div class="flex items-center gap-4 mb-5">
                    <div class="h-12 w-12 rounded-full bg-white/10 flex items-center justify-center text-white font-bold text-lg border border-white/5">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-base text-white capitalize">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-white/30 lowercase italic">{{ Auth::user()->role }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('profile.edit') }}" class="py-3 text-center text-sm font-bold text-white bg-white/5 rounded-xl border border-white/5 transition active:bg-white/10">Edit Profil</a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-3 text-center text-sm font-bold text-red-400 bg-red-400/10 rounded-xl border border-red-400/20">Keluar</button>
                    </form>
                </div>
            </div>
        @else
            <div class="py-8 px-6 space-y-4">
                <a href="{{ route('login') }}" class="block w-full text-center py-4 text-sm font-bold text-white bg-white/5 rounded-xl border border-white/10">Masuk</a>
                <a href="{{ route('register') }}" class="block w-full text-center py-4 text-sm font-bold text-[#3C4142] bg-white rounded-xl shadow-xl">Daftar Akun Baru</a>
            </div>
        @endauth
    </div>
</nav>