<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil Seller berdasarkan email
        $seller1 = User::where('email', 'seller1@squareone.com')->first();
        $seller2 = User::where('email', 'seller2@squareone.com')->first();
        $seller3 = User::where('email', 'seller3@squareone.com')->first();

        if (!$seller1 || !$seller2 || !$seller3) {
            $this->command->error('❌ Seller tidak ditemukan! Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        // ============================================================
        // SELLER 1: ELEKTRONIK (6 Produk)
        // ============================================================
        $elektronik = Category::where('slug', 'elektronik')->first();
        
        $produkElektronik = [
            ['name' => 'Laptop ASUS ROG', 'price' => 15000000, 'stock' => 10, 'description' => 'Laptop gaming spesifikasi tinggi untuk gaming dan editing.'],
            ['name' => 'Mouse Logitech G502', 'price' => 450000, 'stock' => 50, 'description' => 'Mouse gaming presisi tinggi dengan sensor HERO.'],
            ['name' => 'Keyboard Mechanical RGB', 'price' => 850000, 'stock' => 30, 'description' => 'Keyboard mekanikal dengan lampu RGB untuk gaming.'],
            ['name' => 'Headset Sony WH-1000XM5', 'price' => 4500000, 'stock' => 15, 'description' => 'Headset wireless noise cancelling terbaik di kelasnya.'],
            ['name' => 'Monitor LG 27 Inch 4K', 'price' => 5200000, 'stock' => 20, 'description' => 'Monitor 4K UHD untuk gaming dan desain grafis.'],
            ['name' => 'Webcam Logitech C920', 'price' => 1200000, 'stock' => 25, 'description' => 'Webcam Full HD 1080p untuk streaming dan meeting.'],
        ];

        foreach ($produkElektronik as $item) {
            Product::create([
                'seller_id' => $seller1->id,
                'category_id' => $elektronik->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(5),
                'price' => $item['price'],
                'stock' => $item['stock'],
                'description' => $item['description'],
                'image' => null,
            ]);
        }

        // ============================================================
        // SELLER 2: PAKAIAN PRIA (6 Produk)
        // ============================================================
        $pakaianPria = Category::where('slug', 'pakaian-pria')->first();
        
        $produkPria = [
            ['name' => 'Kemeja Formal Putih', 'price' => 250000, 'stock' => 40, 'description' => 'Kemeja formal slim fit untuk acara resmi.'],
            ['name' => 'Celana Jeans Levi\'s', 'price' => 850000, 'stock' => 30, 'description' => 'Celana jeans original Levi\'s regular fit.'],
            ['name' => 'Jaket Bomber Hitam', 'price' => 450000, 'stock' => 25, 'description' => 'Jaket bomber stylish untuk tampilan kasual.'],
            ['name' => 'Kaos Polos Cotton', 'price' => 120000, 'stock' => 100, 'description' => 'Kaos polos cotton combed premium.'],
            ['name' => 'Sepatu Nike Air Max', 'price' => 1500000, 'stock' => 20, 'description' => 'Sepatu olahraga Nike Air Max original.'],
            ['name' => 'Topi Baseball Cap', 'price' => 150000, 'stock' => 50, 'description' => 'Topi baseball cap premium untuk gaya kasual.'],
        ];

        foreach ($produkPria as $item) {
            Product::create([
                'seller_id' => $seller2->id,
                'category_id' => $pakaianPria->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(5),
                'price' => $item['price'],
                'stock' => $item['stock'],
                'description' => $item['description'],
                'image' => null,
            ]);
        }

        // ============================================================
        // SELLER 2: PAKAIAN WANITA (6 Produk)
        // ============================================================
        $pakaianWanita = Category::where('slug', 'pakaian-wanita')->first();
        
        $produkWanita = [
            ['name' => 'Dress Casual Floral', 'price' => 350000, 'stock' => 30, 'description' => 'Dress casual motif floral untuk acara santai.'],
            ['name' => 'Blouse Sifon Premium', 'price' => 280000, 'stock' => 40, 'description' => 'Blouse sifon premium untuk tampilan elegan.'],
            ['name' => 'Rok Midi Plisket', 'price' => 320000, 'stock' => 35, 'description' => 'Rok midi plisket untuk gaya feminin.'],
            ['name' => 'Hijab Segi Empat', 'price' => 85000, 'stock' => 100, 'description' => 'Hijab segi empat voal premium.'],
            ['name' => 'Tas Kulit Wanita', 'price' => 650000, 'stock' => 20, 'description' => 'Tas kulit sintetis premium untuk wanita.'],
            ['name' => 'Sepatu Heels 5cm', 'price' => 450000, 'stock' => 25, 'description' => 'Sepatu heels 5cm untuk tampilan formal.'],
        ];

        foreach ($produkWanita as $item) {
            Product::create([
                'seller_id' => $seller2->id,
                'category_id' => $pakaianWanita->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(5),
                'price' => $item['price'],
                'stock' => $item['stock'],
                'description' => $item['description'],
                'image' => null,
            ]);
        }

        // ============================================================
        // SELLER 3: KESEHATAN (6 Produk)
        // ============================================================
        $kesehatan = Category::where('slug', 'kesehatan')->first();
        
        $produkKesehatan = [
            ['name' => 'Vitamin C 1000mg', 'price' => 150000, 'stock' => 100, 'description' => 'Suplemen vitamin C untuk daya tahan tubuh.'],
            ['name' => 'Masker N95 Isi 50', 'price' => 250000, 'stock' => 80, 'description' => 'Masker N95 isi 50 pcs untuk perlindungan maksimal.'],
            ['name' => 'Hand Sanitizer 500ml', 'price' => 45000, 'stock' => 150, 'description' => 'Hand sanitizer gel 500ml untuk kebersihan tangan.'],
            ['name' => 'Termometer Digital', 'price' => 120000, 'stock' => 40, 'description' => 'Termometer digital infrared non-contact.'],
            ['name' => 'Alat Cek Gula Darah', 'price' => 350000, 'stock' => 30, 'description' => 'Alat cek gula darah digital praktis.'],
            ['name' => 'Tensimeter Digital', 'price' => 450000, 'stock' => 25, 'description' => 'Tensimeter digital untuk cek tekanan darah.'],
        ];

        foreach ($produkKesehatan as $item) {
            Product::create([
                'seller_id' => $seller3->id,
                'category_id' => $kesehatan->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(5),
                'price' => $item['price'],
                'stock' => $item['stock'],
                'description' => $item['description'],
                'image' => null,
            ]);
        }

        // ============================================================
        // SELLER 3: HOBI & MAINAN (6 Produk)
        // ============================================================
        $hobi = Category::where('slug', 'hobi-mainan')->first();
        
        $produkHobi = [
            ['name' => 'Lego City Police', 'price' => 850000, 'stock' => 20, 'description' => 'Lego City Police set lengkap untuk anak-anak.'],
            ['name' => 'Hot Wheels Track Set', 'price' => 450000, 'stock' => 30, 'description' => 'Hot Wheels track set dengan loop 360 derajat.'],
            ['name' => 'Puzzle 1000 Pieces', 'price' => 250000, 'stock' => 40, 'description' => 'Puzzle 1000 pieces dengan gambar pemandangan.'],
            ['name' => 'Drone Mini Camera', 'price' => 1200000, 'stock' => 15, 'description' => 'Drone mini dengan kamera HD untuk hobi fotografi.'],
            ['name' => 'Remote Control Car', 'price' => 650000, 'stock' => 25, 'description' => 'Mobil remote control off-road dengan baterai rechargeable.'],
            ['name' => 'Board Game Monopoly', 'price' => 350000, 'stock' => 35, 'description' => 'Board game Monopoly original untuk keluarga.'],
        ];

        foreach ($produkHobi as $item) {
            Product::create([
                'seller_id' => $seller3->id,
                'category_id' => $hobi->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . Str::random(5),
                'price' => $item['price'],
                'stock' => $item['stock'],
                'description' => $item['description'],
                'image' => null,
            ]);
        }

        $this->command->info('✅ Berhasil menambahkan 30 Produk ke 3 Toko dengan 5 Kategori!');
    }
}