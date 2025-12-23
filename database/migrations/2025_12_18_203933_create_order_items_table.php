<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Order Utama
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Relasi ke Produk (Tetap simpan ID walau produk dihapus, biar history aman)
            // Kita pakai foreignId tapi tanpa constrained ketat biar kalau produk dihapus seller, history order gak error
            $table->unsignedBigInteger('product_id'); 
            
            // Snapshot Data (Penting! Simpan nama/harga/gambar SAAT BELI)
            // Jadi kalau Seller ubah harga/nama nanti, data di order ini tidak berubah.
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->decimal('price', 15, 2); // Harga satuan
            $table->integer('quantity');
            $table->decimal('subtotal', 15, 2); // price * quantity
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};