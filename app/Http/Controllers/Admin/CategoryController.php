<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // 🔥 PENTING: Import Storage

class CategoryController extends Controller
{
    // 1. TAMPILKAN DAFTAR KATEGORI
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    // 2. SIMPAN KATEGORI BARU
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'image' => 'nullable|image|max:2048', // Validasi gambar
        ]);

        // Proses Upload Gambar (Jika Ada)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath, // Simpan path gambar
            'is_active' => true
        ]);

        return back()->with('success', 'Kategori baru ditambahkan ke SquareOne!');
    }

    // 3. TAMPILKAN FORM EDIT (Baru)
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    // 4. PROSES UPDATE DATA (Baru)
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // Gambar opsional pas edit
        ]);

        // Update Nama & Slug
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        // Cek apakah Admin upload gambar baru?
        if ($request->hasFile('image')) {
            // Hapus gambar lama dulu (biar storage gak penuh)
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            // Simpan gambar baru
            $category->image = $request->file('image')->store('categories', 'public');
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    // 5. HAPUS KATEGORI
    public function destroy(Category $category)
    {
        // Hapus file gambar dari storage jika ada
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        
        return back()->with('success', 'Kategori dan gambarnya berhasil dihapus.');
    }
}