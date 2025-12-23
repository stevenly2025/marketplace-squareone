<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner; // <--- PENTING: Jangan lupa import Model Banner ini

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // --- LOGIKA PRODUK (YANG LAMA) ---
        $query = Product::query();

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 2. Filter Kategori
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 3. Ambil Data Produk (Query Utama - FIXED)
        $products = $query->with(['seller', 'category', 'reviews']) 
            // Trik 'as orders_count' supaya tidak perlu ubah view welcome.blade.php
            ->withSum('orderItems as orders_count', 'quantity') 
            ->withAvg('reviews', 'rating')
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        // --- DATA PENDUKUNG ---

        // 4. Ambil data kategori untuk menu icon di atas
        $categories = Category::where('is_active', true)->get();

        // 5. 🔥 AMBIL DATA BANNER (INI YANG BARU) 🔥
        // Kita ambil banner yang statusnya 'is_active' = true, urutkan dari yang terbaru
        $banners = Banner::where('is_active', true)->latest()->get();

        // 6. Kirim SEMUA variabel ($products, $categories, $banners) ke view
        return view('welcome', compact('products', 'categories', 'banners'));
    }
}