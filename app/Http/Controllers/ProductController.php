<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Menampilkan produk milik seller yang sedang login
     */
    public function index()
    {
        $products = Product::where('seller_id', auth()->id())
            ->with('category')
            ->withSum('orderItems as orders_count', 'quantity') // ✅ Pakai alias
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    /**
     * Form tambah produk
     */
    public function create()
    {
        if (!auth()->user()->vendor_payment_info) {
            return redirect()->route('profile.edit')
                ->with('error', '⛔ Eits, tunggu dulu! Kamu WAJIB upload QRIS Toko sebelum mulai berjualan.');
        }

        $categories = Category::where('is_active', true)->get();
        return view('seller.products.create', compact('categories'));
    }

    /**
     * Simpan produk ke database
     */
    public function store(Request $request)
    {
        if (!auth()->user()->vendor_payment_info) {
            return redirect()->route('profile.edit')
                ->with('error', '⛔ Upload QRIS dulu sebelum menambah produk!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'seller_id' => auth()->id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Produk SquareOne berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail produk (untuk halaman publik)
     * ✅ FINAL FIX - Pakai alias 'as orders_count' biar konsisten
     */
    public function show($slug)
    {
        $product = Product::with(['seller', 'category', 'reviews.user'])
            // 1. Hitung Rata-rata Rating -> Hasilnya: reviews_avg_rating
            ->withAvg('reviews', 'rating')
            
            // 2. Hitung Jumlah Review -> Hasilnya: reviews_count
            ->withCount('reviews')
            
            // 3. Hitung Jumlah Barang Terjual (Quantity)
            // ✅ Trik: Kita namakan hasilnya 'orders_count' biar view gak bingung
            ->withSum('orderItems as orders_count', 'quantity')
            
            ->where('slug', $slug)
            ->where('is_active', true) // Pastikan hanya produk aktif
            ->firstOrFail();

        // Guard: Pastikan nilainya 0 kalau null (biar gak error di view)
        $product->reviews_avg_rating = $product->reviews_avg_rating ?? 0;
        $product->reviews_count = $product->reviews_count ?? 0;
        $product->orders_count = $product->orders_count ?? 0;

        return view('products.show', compact('product'));
    }

    /**
     * Form edit produk
     */
    public function edit(Product $product)
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Ini bukan produk Anda.');
        }

        $categories = Category::where('is_active', true)->get();
        return view('seller.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk
     */
    public function update(Request $request, Product $product)
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Ini bukan produk Anda.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // ✅ DIPERBAIKI: 'image' bukan 'images'
        ]);

        $updateData = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ];

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $updateData['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($updateData);

        return redirect()->route('seller.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Hapus produk
     */
    public function destroy(Product $product)
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Ini bukan produk Anda.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus dari SquareOne.');
    }

    /**
     * Toggle status aktif/nonaktif produk
     */
    public function toggleStatus(Product $product)
    {
        if ($product->seller_id !== auth()->id()) {
            abort(403, 'Akses ditolak. Ini bukan produk Anda.');
        }

        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Produk berhasil {$status}!");
    }
}