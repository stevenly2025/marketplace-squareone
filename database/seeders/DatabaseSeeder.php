<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Seeder MASTER DATA (Pulau & Kota)
        |--------------------------------------------------------------------------
        */
        $this->call([
            CitySeeder::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Buat Akun SuperAdmin
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'Agnes SuperAdmin',
            'email' => 'admin@squareone.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone' => '081234567890',
            'address' => 'Kantor Pusat SquareOne, Medan',
            'city' => 'Medan',
            'email_verified_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Buat 3 Akun Seller (Untuk Berbagai Kategori)
        |--------------------------------------------------------------------------
        */
        
        // SELLER 1: Toko Elektronik
        User::create([
            'name' => 'Toko Elektronik Maju',
            'email' => 'seller1@squareone.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '08111111111',
            'address' => 'Jl. Gatot Subroto No. 10, Medan',
            'city' => 'Medan',
            'email_verified_at' => now(),
            'vendor_payment_info' => null,
        ]);

        // SELLER 2: Toko Fashion Pria & Wanita
        User::create([
            'name' => 'Fashion Store Indonesia',
            'email' => 'seller2@squareone.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '08222222222',
            'address' => 'Jl. Sudirman No. 25, Jakarta Pusat',
            'city' => 'Jakarta Pusat',
            'email_verified_at' => now(),
            'vendor_payment_info' => null,
        ]);

        // SELLER 3: Toko Kesehatan & Hobi
        User::create([
            'name' => 'Toko Sehat & Hobi',
            'email' => 'seller3@squareone.com',
            'password' => Hash::make('password'),
            'role' => 'seller',
            'phone' => '08333333333',
            'address' => 'Jl. Asia Afrika No. 50, Bandung',
            'city' => 'Bandung',
            'email_verified_at' => now(),
            'vendor_payment_info' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Buat Akun Buyer Contoh
        |--------------------------------------------------------------------------
        */
        User::create([
            'name' => 'Budi Pembeli',
            'email' => 'buyer@squareone.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '081122334455',
            'address' => 'Jl. Merdeka No. 45, Bandung',
            'city' => 'Bandung',
            'email_verified_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Seeder Kategori Produk
        |--------------------------------------------------------------------------
        */
        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik'],
            ['name' => 'Pakaian Pria', 'slug' => 'pakaian-pria'],
            ['name' => 'Pakaian Wanita', 'slug' => 'pakaian-wanita'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan'],
            ['name' => 'Hobi & Mainan', 'slug' => 'hobi-mainan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Jalankan Seeder Produk
        |--------------------------------------------------------------------------
        */
        $this->call([
            ProductSeeder::class,
        ]);
    }
}