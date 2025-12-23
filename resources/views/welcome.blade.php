<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SquareOne - Marketplace</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'square-main': '#3C4142', // Charcoal
                        'square-dark': '#2b2f30',
                        'square-accent': '#5C6364', 
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <header class="bg-square-main text-white sticky top-0 z-50 shadow-lg">
        
        <div class="max-w-[1200px] mx-auto px-4 flex justify-between items-center text-[12px] py-1.5 font-light text-gray-300">
            <div class="flex items-center space-x-4 hidden md:flex">
                
                {{-- LINK SELLER CENTRE --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-white transition font-bold text-yellow-400">Seller Centre</a>
                @else
                    <a href="{{ route('seller.register') }}" class="hover:text-white transition">Seller Centre</a>
                @endauth

            </div>

            <div class="flex items-center space-x-4 ml-auto">
                
                <div class="flex items-center space-x-3 ml-2 font-medium text-white">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="hover:text-gray-200 border border-gray-500 px-3 py-0.5 rounded-full text-[11px] hover:bg-white hover:text-square-main transition">{{ Auth::user()->name }}</a>
                    @else
                        <a href="{{ route('register') }}" class="hover:text-white font-bold">Daftar</a>
                        <span class="border-r border-gray-500 h-3"></span>
                        <a href="{{ route('login') }}" class="hover:text-white font-bold">Log In</a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="max-w-[1200px] mx-auto px-4 pb-5 pt-3">
            <div class="flex items-center gap-4 md:gap-8">
                <a href="/" class="flex-shrink-0 flex items-center gap-3 text-white no-underline group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 w-auto object-contain bg-white rounded p-0.5"> 
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold tracking-tighter leading-none group-hover:text-gray-200 transition">SquareOne</span>
                        <span class="text-[10px] tracking-widest text-gray-400 uppercase">Official Market</span>
                    </div>
                </a>

                <div class="flex-1 relative max-w-4xl mx-4">
                    <form action="/" method="GET" class="w-full relative shadow-lg">
                        <input type="text" name="search" placeholder="Cari kebutuhanmu di sini..." 
                               class="w-full pl-5 pr-14 py-2.5 rounded-md text-sm text-gray-800 outline-none border-2 border-transparent focus:border-gray-500 transition shadow-inner">
                        <button type="submit" class="absolute right-1 top-1 bottom-1 bg-square-main hover:bg-black text-white px-5 rounded-md transition flex items-center justify-center">
                            <i class="fas fa-search text-sm"></i>
                        </button>
                    </form>
                </div>

                {{-- KERANJANG BELANJA DINAMIS --}}
                <div class="flex-shrink-0 relative group cursor-pointer">
                    <a href="{{ auth()->check() && auth()->user()->role === 'buyer' ? route('buyer.cart.index') : route('login') }}" class="block p-2">
                        
                        <i class="fas fa-shopping-cart text-2xl text-gray-300 group-hover:text-white transition transform group-hover:scale-105"></i>
                        
                        {{-- Logic Badge Angka --}}
                        @auth
                            @if(auth()->user()->role === 'buyer')
                                @php
                                    // 🔥 PERBAIKAN: Menggunakan buyer_id sesuai instruksi
                                    $cartCount = \App\Models\Cart::where('buyer_id', auth()->id())->count();
                                @endphp
                                
                                @if($cartCount > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-square-main shadow-sm">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            @endif
                        @endauth

                    </a>
                </div>

            </div>
        </div>
    </header>

    <main class="max-w-[1200px] mx-auto px-4 py-8">
        
        {{-- BANNER SLIDER --}}
        <div class="rounded-xl overflow-hidden shadow-md mb-8 relative group bg-white border border-gray-100">
            <div class="flex overflow-x-auto snap-x snap-mandatory scrollbar-hide">
                
                @forelse($banners as $banner)
                    <div class="snap-center flex-shrink-0 w-full relative aspect-[3/1] md:aspect-[3.5/1]">
                        <img src="{{ asset('storage/' . $banner->image) }}" class="w-full h-full object-contain bg-gray-50">
                        @if($banner->link)
                            <a href="{{ $banner->link }}" class="absolute inset-0 z-10"></a>
                        @endif
                    </div>
                @empty
                    <div class="snap-center flex-shrink-0 w-full h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                        <div class="text-center">
                            <i class="fas fa-image text-4xl mb-2"></i>
                            <p class="font-bold">Belum ada banner promo</p>
                        </div>
                    </div>
                @endforelse

            </div>
        </div>

        {{-- KATEGORI PILIHAN --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
            <div class="text-center mb-6">
                 <h3 class="text-square-main font-bold uppercase text-sm border-b-2 border-square-main pb-2 inline-block">Kategori Pilihan</h3>
            </div>
           
            <div class="flex flex-wrap justify-center gap-6 md:gap-10 pb-2 px-2">
                @foreach($categories as $category)
                <a href="/?category={{ $category->slug }}" class="group min-w-[80px] flex flex-col items-center gap-3 cursor-pointer hover:-translate-y-1 transition duration-300">
                    
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl flex items-center justify-center bg-gray-50 border border-gray-200 group-hover:bg-square-main group-hover:text-white group-hover:border-square-main transition duration-300 shadow-sm overflow-hidden p-2">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" class="w-full h-full object-contain">
                        @else
                            <span class="text-2xl">📦</span>
                        @endif
                    </div>
                    
                    <span class="text-xs font-bold text-gray-600 group-hover:text-square-main max-w-[100px] text-center leading-tight">
                        {{ $category->name }}
                    </span>
                </a>
                @endforeach
            </div>
        </div>

        <div id="produk">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2 h-8 bg-square-main rounded-full"></span>
                    Rekomendasi Untukmu
                </h2>
                <a href="#" class="text-sm text-square-main font-semibold hover:underline">Lihat Semua ></a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 md:gap-4">
                @forelse($products as $product)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 group overflow-hidden flex flex-col h-full">
                    <a href="{{ route('products.show', $product->slug) }}" class="flex flex-col h-full">
                        
                        <div class="relative aspect-square bg-gray-100 overflow-hidden">
                            @if($product->image) {{-- DIPERBAIKI: 'image' bukan 'images' --}}
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300 text-xs flex-col gap-1">
                                    <i class="fas fa-image text-2xl"></i>
                                    <span>No Image</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-3 flex flex-col flex-grow justify-between">
                            <div>
                                <h3 class="text-sm text-gray-800 font-medium line-clamp-2 mb-2 leading-snug min-h-[40px]">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-gray-900 font-bold text-base mb-1">
                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                <div class="flex items-center gap-1 mb-2">
                                    <i class="fas fa-map-marker-alt text-[10px] text-gray-400"></i>
                                    <span class="text-[11px] text-gray-500 truncate">
                                        {{ $product->seller?->city ?? 'Indonesia' }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-100 pt-2 mt-1">
                                <div class="flex items-center gap-1">
                                    @php $rating = $product->reviews_avg_rating ?? 0; @endphp
                                    @if($rating > 0)
                                        <i class="fas fa-star text-yellow-400 text-[10px]"></i>
                                        <span class="text-[11px] text-gray-600">{{ number_format($rating, 1) }}</span>
                                    @else
                                        <div class="flex text-[9px] text-gray-300 gap-0.5">
                                            <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="text-[10px] text-gray-500">
                                    @if(($product->orders_count ?? 0) > 0)
                                        {{ $product->orders_count }} Terjual
                                    @else
                                        Baru
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-span-full py-16 text-center bg-white rounded-lg border border-dashed border-gray-300">
                    <div class="text-4xl mb-3 text-gray-200">🛍️</div>
                    <p class="text-gray-500 font-medium text-sm">Belum ada produk yang tersedia.</p>
                </div>
                @endforelse
            </div>

            <div class="mt-10 flex justify-center">
                {{ $products->links() }}
            </div>
        </div>
    </main>

    <footer class="bg-square-main text-white mt-16">
        <div class="max-w-[1200px] mx-auto px-4 pt-12 pb-8">
            
            {{-- BAGIAN ATAS FOOTER --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                
                {{-- LOGO & DESKRIPSI --}}
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 w-auto object-contain bg-white rounded p-1"> 
                        <div class="flex flex-col">
                            <span class="text-2xl font-extrabold tracking-tighter leading-none">SquareOne</span>
                            <span class="text-[10px] tracking-widest text-gray-400 uppercase">Official Market</span>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-300 max-w-md mb-4">
                        Platform marketplace terpercaya yang menghubungkan pembeli dan penjual di seluruh Indonesia. Belanja mudah, aman, dan terpercaya.
                    </p>
                    <div class="flex items-center gap-4 mt-4">
                        <a href="#" class="w-9 h-9 rounded-full bg-square-dark hover:bg-white hover:text-square-main transition flex items-center justify-center">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-square-dark hover:bg-white hover:text-square-main transition flex items-center justify-center">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-square-dark hover:bg-white hover:text-square-main transition flex items-center justify-center">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="w-9 h-9 rounded-full bg-square-dark hover:bg-white hover:text-square-main transition flex items-center justify-center">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                {{-- LAYANAN PELANGGAN --}}
                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-xs tracking-wider border-b border-gray-600 pb-2">Layanan Pelanggan</h4>
                    <ul class="space-y-2.5 text-sm text-gray-300">
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Cara Berbelanja</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Pengembalian Barang</a></li>
                    </ul>
                </div>
                
                {{-- TENTANG KAMI --}}
                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-xs tracking-wider border-b border-gray-600 pb-2">Tentang SquareOne</h4>
                    <ul class="space-y-2.5 text-sm text-gray-300">
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Karir</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Blog</a></li>
                        <li><a href="#" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-[8px]"></i> Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>

            {{-- GARIS PEMISAH --}}
            <div class="border-t border-gray-600 pt-6">
                <div class="text-center text-xs text-gray-400">
                    <p>&copy; {{ date('Y') }} SquareOne Marketplace. Platform Jual Beli Online Terpercaya di Indonesia.</p>
                </div>
            </div>

        </div>
    </footer>

    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around p-2 z-50 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        <a href="/" class="flex flex-col items-center text-square-main">
            <i class="fas fa-home text-lg"></i><span class="text-[10px] mt-1 font-bold">Beranda</span>
        </a>
        <a href="#" class="flex flex-col items-center text-gray-400 hover:text-square-main">
            <i class="fas fa-compass text-lg"></i><span class="text-[10px] mt-1">Jelajah</span>
        </a>
        
        {{-- KERANJANG DI MOBILE NAV --}}
        <a href="{{ auth()->check() && auth()->user()->role === 'buyer' ? route('buyer.cart.index') : route('login') }}" class="flex flex-col items-center text-gray-400 hover:text-square-main relative">
            <i class="fas fa-shopping-cart text-lg"></i><span class="text-[10px] mt-1">Keranjang</span>
        </a>

        <a href="/profile" class="flex flex-col items-center text-gray-400 hover:text-square-main">
            <i class="fas fa-user text-lg"></i><span class="text-[10px] mt-1">Akun</span>
        </a>
    </nav>
</body>
</html>