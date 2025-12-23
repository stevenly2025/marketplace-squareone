<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        // Validasi Upload
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB (5120 KB)
            'link'  => 'nullable|url'
        ], [
            // Pesan Error Custom (Bahasa Indonesia)
            'image.required' => 'Wajib pilih gambar dulu, Bro!',
            'image.image'    => 'File harus berupa gambar (JPG, PNG, JPEG).',
            'image.max'      => 'Ukuran gambar kegedean! Maksimal 5MB ya.',
            'link.url'       => 'Format link tidak valid (harus pakai http:// atau https://).'
        ]);

        // Simpan File ke Storage
        $path = $request->file('image')->store('banners', 'public');

        // Simpan ke Database
        Banner::create([
            'image' => $path,
            'link'  => $request->link,
        ]);

        return back()->with('success', 'Banner berhasil diupload!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Hapus file fisik di storage
        Storage::disk('public')->delete($banner->image);
        
        // Hapus data di database
        $banner->delete();

        return back()->with('success', 'Banner berhasil dihapus!');
    }
}